using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using Android;
using Android.App;
using Android.Content;
using Android.Graphics;
using Android.Graphics.Drawables;
using Android.OS;
using Android.Provider;
using Android.Support.V7.Widget_PanGolin;
using Android.Util;
using Android.Views;
using Android.Webkit;
using Android.Widget;
using AX_Inject.AuthDialog.adapter;
using AX_Inject.AuthDialog.api;
using AX_Inject.AuthDialog.api.KfkModel;
using AX_Inject.AuthDialog.model;
using AX_Inject.AuthDialog.util;
using AX_Inject.AuthDialog.view;
using Java.Lang;
using Java.Util;
using Java.Util.Jar;
using Java.Util.Zip;

namespace AX_Inject
{
    [Activity(Label = "@string/app_name", Theme = "@style/AppTheme", MainLauncher = true)]
    public class MainActivity : Activity
            , HttpApi.Result<XCodeInfo>
            , HttpApi.Result<XAppInfo>
            , HttpApi.Result<XLoginInfo>
            , HttpApi.Result<XCheckInfo>
            , HttpApi.Result<XTrialInfo>
            , HttpApi.Result<XQueryInfo>
            , HttpApi.Result<KfkPageData>
            , HttpApi.Result<Build_order_Info>
            , HttpApi.Result<Card_Info>
    {
        private View view;
        private string mCard;
        private XAppInfo XAppInfo;
        private Runnable run;
        private bool IS_Run = false;
        private AlertDialog paydialog;
        public void next(XCodeInfo data)
        {
            Java.Util.Jar.Manifest manifest = new Java.Util.Jar.Manifest();
            ZipFile zip = new ZipFile(ApplicationInfo.SourceDir);
            manifest.Read(zip.GetInputStream(zip.GetEntry(JarFile.ManifestName)));
            string Anti = manifest.MainAttributes.GetValue("PanGolin-Anti");
            if (string.IsNullOrEmpty(Anti))
                Anti = "";
            bool flag = false;
            foreach (XSignInfo.mdata a in AuthDialog.Dialog.XSignInfo.data)
                if (a.anti.Equals(Anti)) flag = true;
            if (!flag)
            {
                AntiDialog(this);
                return;
            }
            switch (data.code)
            {
                case 200:
                    mCard = data.data.code;
                    HttpApi.loginAsync(this, mCard, mac.GetMac());
                    break;
                default:
                    if (File.Exists(CacheDir.Path + "/card.dat"))
                    {
                        string card = File.ReadAllText(CacheDir.Path + "/card.dat");
                        if (string.Empty != card)
                        {
                            mCard = card;
                            HttpApi.loginAsync(this, card, mac.GetMac());
                            return;
                        }
                    }
                    HttpApi.InitAsync(this);
                    break;
            }
        }

        public void next(XAppInfo data)
        {
            IS_Run = true;
            if (data.code == 404)
                System.Environment.Exit(0);
            XAppInfo = data;
            Loading.Hide();
            if (data.data.authmode == 0)
                Init();
            else
                Toast.MakeText(this, "应用模式不一致", ToastLength.Long).Show();
        }

        public void next(XLoginInfo data)
        {
            Loading.Hide();
            switch (data.code)
            {
                case 200:
                    StreamWriter streamWriter = File.CreateText(CacheDir.Path + "/card.dat");
                    streamWriter.Write(mCard);
                    streamWriter.Close();
                    AuthDialog.Dialog.AntiRun = true;
                    Toast.MakeText(this, data.msg + " 到期时间:" + data.time, ToastLength.Long).Show();
                    Check(data.token, true, mCard);
                    StartActivity(new Intent(this, Java.Lang.Class.ForName(AuthDialog.Dialog.Properties.GetProperty("MainClass"))));
                    Finish();
                    break;
                default:
                    if (!IS_Run)
                    {
                        File.Delete(CacheDir.Path + "/card.dat");
                        HttpApi.InitAsync(this);
                    }
                    Toast.MakeText(this, data.msg, ToastLength.Long).Show();
                    break;
            }
        }

        public void next(XCheckInfo data)
        {
            if (data.code == 404)
            {
                Finish();
                System.Environment.Exit(0);
            }
        }

        public void next(XTrialInfo data)
        {
            Loading.Hide();
            switch (data.code)
            {
                case 200:
                    AuthDialog.Dialog.AntiRun = true;
                    Toast.MakeText(this, data.msg + " 到期时间:" + data.time, ToastLength.Long).Show();
                    Check(data.token, false, "");
                    StartActivity(new Intent(this, Java.Lang.Class.ForName(AuthDialog.Dialog.Properties.GetProperty("MainClass"))));
                    Finish();
                    break;
                default:
                    Toast.MakeText(this, data.msg, ToastLength.Long).Show();
                    break;
            }
        }

        public void next(XQueryInfo data)
        {
            switch (data.code)
            {
                case 200:
                    new AlertDialog.Builder(this, 4)
                        .SetTitle("查询成功")
                        .SetMessage(
                         "注册码:" + data.data.code + "\n"
                        + "绑定机器码:" + data.data.computer_uid + "\n"
                        + "时长:" + data.data.time_str + "\n"
                        + "使用次数:" + data.data.use_count + "\n"
                        + "是否过期:" + (data.data.overdue == 1 ? "已过期" : "未到期") + "\n"
                        + "是否冻结:" + (data.data.frozen == 1 ? "已冻结" : "未冻结"))
                        .SetCancelable(false)
                        .SetNegativeButton("取消", delegate
                        {
                        })
                        .Show();
                    break;
                default:
                    Toast.MakeText(this, data.msg, ToastLength.Long).Show();
                    break;
            }
        }

        public void next(KfkPageData data)
        {
            Loading.Hide();
            RelativeLayout relativeLayout = new RelativeLayout(this);
            View view = LayoutInflater.From(this).Inflate(Assets.OpenXmlResourceParser("res/layout/xamarin_pay.xml"), relativeLayout);
            CardView cardView = (CardView)view.FindViewWithTag("cardview");
            cardView.Radius = 45;
            cardView.Elevation = 1;
            cardView.PreventCornerOverlap = false;
            Spinner Goods = (Spinner)view.FindViewWithTag("goods");
            Goods.Adapter = new GoodsAdapter(this, data.data);
            Spinner Products = (Spinner)view.FindViewWithTag("products");
            Button button = (Button)view.FindViewWithTag("Pay");
            AlertDialog pay = new AlertDialog.Builder(this, 4)
                .SetView(relativeLayout)
                .Create();
            pay.Show();
            pay.Window.SetBackgroundDrawableResource(Android.Resource.Color.Transparent);
            Goods.ItemSelected += (v, i) =>
            {
                Products.Adapter = new ProductAdapter(this, data.data[i.Position].products);
            };
            button.Click += delegate
            {
                List<string> item = new List<string>();
                if (data.ZfbPay)
                    item.Add("支付宝支付");
                if (data.WxPay)
                    item.Add("微信支付");
                if (data.QQPay)
                    item.Add("QQ钱包支付");
                AlertDialog PayType = null;
                PayType = new AlertDialog.Builder(this, 5).SetTitle("请选择支付方式")
                .SetSingleChoiceItems(item.ToArray(), -1, (dialog, pos) =>
                {
                    //创建订单
                    Loading.Show(this);
                    int p = 0;
                    switch (item[pos.Which])
                    {
                        case "支付宝支付":
                            p = 1;
                            break;
                        case "微信支付":
                            p = 2;
                            break;
                        case "QQ钱包支付":
                            p = 3;
                            break;
                    }
                    HttpApi.Build_order(this, data.data[Goods.SelectedItemPosition].products[Products.SelectedItemPosition].product_id, p, data.buyer_token, XAppInfo.data.weburl);
                    PayType.Dismiss();
                })
                .Show();
            };
        }

        public class Web : WebViewClient
        {
            private WebView webView;
            private Context context;
            public Web(WebView webView, Context context)
            {
                this.webView = webView;
                this.context = context;
            }
            public override bool ShouldOverrideUrlLoading(WebView view, string url)
            {
                if (url.StartsWith("mqqapi://"))
                {
                    Intent intent = new Intent();
                    intent.SetAction(Intent.ActionView);
                    intent.SetData(Android.Net.Uri.Parse(url));
                    context.StartActivity(intent);
                    return true;
                }
                webView.LoadUrl(url);
                return false;
            }
        }
        public void next(Build_order_Info data)
        {
            Loading.Hide();
            if (data.data.paytype.Equals("QRCODE"))
            {
                if (data.data.paysoft == 1 || data.data.paysoft == 3)
                {
                    paydialog = new AlertDialog.Builder(this, 5)
                    .SetTitle("正在拉起支付")
                    .SetCancelable(false)
                    .SetMessage("特别注意:\n支付过程中请勿关闭该窗口 避免获取支付结果失败\n支付完成后请等待10秒左右获取支付结果")
                    .SetNeutralButton("取消支付", delegate { })
                    .Show();
                    string url = data.data.payurl.Replace("http://api.kuaifaka.com/tool/qrcode/make?data=", "");
                    url = Android.Net.Uri.Decode(url);
                    if (data.data.paysoft == 1)
                    {
                        Intent intent = new Intent();
                        intent.SetAction("android.intent.action.VIEW");
                        intent.SetData(Android.Net.Uri.Parse("alipayqr://platformapi/startapp?saId=10000007&qrcode=" + url));
                        if (intent.ResolveActivity(PackageManager) != null)
                        {
                            StartActivity(intent);
                            return;
                        }
                        intent.SetData(Android.Net.Uri.Parse(url));
                        StartActivity(intent);
                    }
                    else if (data.data.paysoft == 3)
                    {
                        WebView webView = new WebView(this);
                        webView.Settings.JavaScriptEnabled = true;
                        webView.SetWebViewClient(new Web(webView, this));
                        webView.LoadUrl(url);
                    }
                    Handler handler = new Handler();
                    Runnable runnable = null;
                    runnable = new Runnable(() =>
                    {
                        HttpApi.Get_order_state(this, data.data.order_num);
                        if (paydialog.IsShowing)
                            handler.PostDelayed(runnable, 10 * 1000);
                    });
                    handler.PostDelayed(runnable, 10 * 1000);
                }
                else
                {
                    ImageView imageView = new ImageView(this);
                    paydialog = new AlertDialog.Builder(this, 5)
                        .SetTitle("请截图进行扫码支付")
                        .SetCancelable(false)
                        .SetMessage("特别注意:\n支付过程中请勿关闭该窗口 避免获取支付结果失败\n支付完成后请等待10秒左右获取支付结果")
                        .SetView(imageView)
                        .SetNeutralButton("取消支付", delegate { })
                        .Show();
                    HttpApi.LoadImage2(data.data.payurl, imageView);
                    Handler handler = new Handler();
                    Runnable runnable = null;
                    runnable = new Runnable(() =>
                    {
                        HttpApi.Get_order_state(this, data.data.order_num);
                        if (paydialog.IsShowing)
                            handler.PostDelayed(runnable, 10 * 1000);
                    });
                    handler.PostDelayed(runnable, 10 * 1000);
                }
            }
            else if (data.data.paytype.Equals("HTML"))
            {
                Toast.MakeText(this, "暂时不支持该支付方式", ToastLength.Long).Show();
            }
            else
                Toast.MakeText(this, "暂时不支持该支付方式", ToastLength.Long).Show();

        }

        public void next(Card_Info data)
        {
            if (data.code == 200 && !string.IsNullOrEmpty(data.data.Result))
            {
                EditText code = new EditText(this);
                code.Text = data.data.Result;
                new AlertDialog.Builder(this, 5)
                    .SetTitle("提卡成功")
                    .SetView(code)
                    .SetCancelable(false)
                    .SetNeutralButton("取消", delegate { })
                    .Show();
                paydialog.Dismiss();
            }
        }

        private void Init()
        {
            ((TextView)view.FindViewWithTag("Tips")).SetText(XAppInfo.data.notice, TextView.BufferType.Normal);
            Button bt1 = (Button)view.FindViewWithTag("bt1");
            Button bt2 = (Button)view.FindViewWithTag("bt2");
            Button bt3 = (Button)view.FindViewWithTag("bt3");
            EditText Card = (EditText)view.FindViewWithTag("et");
            if (XAppInfo.data.try_count > 0)
                bt1.SetText("试用", TextView.BufferType.Normal);
            if (string.IsNullOrEmpty(XAppInfo.data.weburl))
                bt2.Visibility = ViewStates.Gone;
            bt1.Click += delegate
            {
                if (XAppInfo.data.try_count > 0)
                {
                    if (string.Empty == mac.GetMac())
                    {
                        new AlertDialog.Builder(this)
                        .SetTitle("请确认你的权限是否打开")
                        .SetMessage("请在权限管理里(找到" + PackageManager.GetApplicationLabel(PackageManager.GetApplicationInfo(PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                        .SetPositiveButton("去开启", (s, e) =>
                        {
                            Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                            Android.Net.Uri uri = Android.Net.Uri.FromParts("package", PackageName, null);
                            intent.SetData(uri);
                            StartActivity(intent);
                        })
                        .Show();
                        return;
                    }
                    Loading.Show(this);
                    HttpApi.trialAsync(this, mac.GetMac());
                }
                else
                {
                    string card = Card.Text.Trim();
                    if (string.Empty == card) return;
                    HttpApi.QueryAsync(this, card);
                }
            };
            bt2.Click += delegate
            {
                if (XAppInfo.data.weburl.StartsWith("https://www.kuaifaka.com/purchasing?link="))
                {
                    Loading.Show(this);
                    HttpApi.GetPage_data(this, XAppInfo.data.weburl);
                }
                else
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(XAppInfo.data.weburl));
                    StartActivity(browserIntent);
                }
            };
            bt3.Click += delegate
            {
                string card = Card.Text.Trim();
                if (string.Empty == card) return;
                if (string.Empty == mac.GetMac())
                {
                    new AlertDialog.Builder(this)
                    .SetTitle("请确认你的权限是否打开")
                    .SetMessage("请在权限管理里(找到" + PackageManager.GetApplicationLabel(PackageManager.GetApplicationInfo(PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                    .SetPositiveButton("去开启", (s, e) =>
                    {
                        Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                        Android.Net.Uri uri = Android.Net.Uri.FromParts("package", PackageName, null);
                        intent.SetData(uri);
                        StartActivity(intent);
                    })
                    .Show();
                    return;
                }
                Loading.Show(this);
                mCard = card;
                HttpApi.loginAsync(this, card, mac.GetMac());
            };
            if (XAppInfo.data.version > Convert.ToInt32(AuthDialog.Dialog.Properties.GetProperty("Ver")))
                new AlertDialog.Builder(this)
                               .SetTitle(XAppInfo.data.title)
                               .SetMessage(XAppInfo.data.update_msg)
                               .SetCancelable(XAppInfo.data.updatemode == 0 ? false : true)
                               .SetPositiveButton("更新", (s, e) =>
                               {
                                   Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(XAppInfo.data.update_url));
                                   StartActivity(browserIntent);
                               }).Show();
        }

        protected override void OnCreate(Bundle savedInstanceState)
        {
            base.OnCreate(savedInstanceState);
            Xamarin.Essentials.Platform.Init(this, savedInstanceState);
            RelativeLayout relativeLayout = new RelativeLayout(this);
            view = LayoutInflater.From(this).Inflate(this.Assets.OpenXmlResourceParser("res/layout/xamarin_activity.xml"), relativeLayout);
            SetContentView(relativeLayout);
            AuthDialog.Dialog.Properties = new Properties();
            string content;
            using (StreamReader sr = new StreamReader(Assets.Open("Conf.dat")))
            {
                content = sr.ReadToEnd();
            }
            Stream stream = new MemoryStream(Android.Util.Base64.Decode(Encoding.UTF8.GetBytes(content), Base64Flags.NoWrap));
            AuthDialog.Dialog.Properties.Load(stream);


            /*AuthDialog.Dialog.Properties = new Properties();
            AuthDialog.Dialog.Properties.SetProperty("Appid", "7");
            AuthDialog.Dialog.Properties.SetProperty("Ver", "1");
            AuthDialog.Dialog.Properties.SetProperty("MainClass", this.Class.Name);*/


            if (AuthDialog.Dialog.AntiRun)
            {
                StartActivity(new Intent(this, Java.Lang.Class.ForName(AuthDialog.Dialog.Properties.GetProperty("MainClass"))));
                Finish();
                return;
            }
            RoundImageView v = (RoundImageView)view.FindViewWithTag("image");
            v.SetImageBitmap(BitmapFactory.DecodeStream(Assets.Open("bj.png")));
            view.FindViewWithTag("mainview").Background = new BitmapDrawable(Resources, BitmapFactory.DecodeStream(Assets.Open("background.png")));
            Loading.Show(this);
            HttpApi.GetCardAsync(this, mac.GetMac());


            /*Button bt1 = (Button)view.FindViewWithTag("bt1");
            bt1.Click += delegate
            {
                Toast.MakeText(this, "xxx", ToastLength.Long).Show();
            };*/
        }

        private void Check(string Token, bool CheckType, string card)
        {
            Handler handler = new Handler();
            run = new Runnable(() =>
            {
                HttpApi.CheckAsync(this, Token, CheckType, mac.GetMac(), card);
                handler.PostDelayed(run, 1000 * 20);
            });
            handler.Post(run);
        }

        private void AntiDialog(Activity activity)
        {
            new AlertDialog.Builder(activity)
               .SetTitle("Piracy warning")
               .SetMessage(AuthDialog.Dialog.XSignInfo.msg)
               .SetCancelable(false)
               .SetPositiveButton("Close", (s, e) =>
               {
                   Finish();
                   System.Environment.Exit(0);
               })
                .Show();
            XAppInfo = null;
        }
    }
}

