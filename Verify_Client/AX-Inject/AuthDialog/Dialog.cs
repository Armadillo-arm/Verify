using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using Android;
using Android.App;
using Android.Content;
using Android.Graphics;
using Android.OS;
using Android.Runtime;
using Android.Support.V7.Widget_PanGolin;
using Android.Util;
using Android.Views;
using Android.Widget;
using AX_Inject.AuthDialog.api;
using AX_Inject.AuthDialog.model;
using AX_Inject.AuthDialog.util;
using AX_Inject.AuthDialog.view;
using Java.Util;
using Java.Util.Jar;
using Java.Util.Zip;
using Newtonsoft.Json;
using static Android.Widget.TextView;
using Android.Preferences;
using Android.Graphics.Drawables;
using AX_Inject.AuthDialog.api.KfkModel;
using AX_Inject.AuthDialog.adapter;
using Android.Webkit;
using Java.Lang;

namespace AX_Inject.AuthDialog
{
    public class Dialog
    {
        public static bool AntiRun = false;
        public static Properties Properties;
        public static XSignInfo XSignInfo;

        private static AlertDialog auth;
        private static View view;
        private static Activity Base;
        private static XAppInfo appInfo;
        private static Java.Lang.Runnable run;
        private static string mCard;
        private static Result mResult;
        private static ISharedPreferences share;
        private static ISharedPreferencesEditor edit;
        private static AlertDialog paydialog;
        private static LinearLayout HookView;
        public static void Show(Activity activity)
        {
            FrameLayout WindowsView = (FrameLayout)activity.Window.DecorView;
            HookView = new LinearLayout(activity);
            HookView.LayoutParameters = new ViewGroup.LayoutParams(ViewGroup.LayoutParams.MatchParent, ViewGroup.LayoutParams.MatchParent);
            HookView.Clickable = true;
            WindowsView.AddView(HookView);
            if (AntiRun)
            {
                HookView.Visibility = ViewStates.Gone;
                return;
            }
            //初始化配置文件
            Properties = new Properties();
            string content;
            using (StreamReader sr = new StreamReader(activity.Assets.Open("Conf.dat")))
            {
                content = sr.ReadToEnd();
            }
            Stream stream = new MemoryStream(Android.Util.Base64.Decode(Encoding.UTF8.GetBytes(content), Base64Flags.NoWrap));
            Properties.Load(stream);

            /*Properties.SetProperty("Appid", "2");
            Properties.SetProperty("Ver", "1");*/

            Base = activity;
            Loading.Show(activity);
            mResult = new Result();
            HttpApi.GetCardAsync(mResult, mac.GetMac());
        }
        private class Result : HttpApi.Result<XCodeInfo>
            , HttpApi.Result<XAppInfo>
            , HttpApi.Result<XLoginInfo>
            , HttpApi.Result<XCheckInfo>
            , HttpApi.Result<XTrialInfo>
            , HttpApi.Result<XQueryInfo>
            , HttpApi.Result<KfkPageData>
            , HttpApi.Result<Build_order_Info>
            , HttpApi.Result<Card_Info>
        {
            //机器码取注册码
            public void next(XCodeInfo data)
            {
                Java.Util.Jar.Manifest manifest = new Java.Util.Jar.Manifest();
                ZipFile zip = new ZipFile(Base.ApplicationInfo.SourceDir);
                manifest.Read(zip.GetInputStream(zip.GetEntry(JarFile.ManifestName)));
                string Anti = manifest.MainAttributes.GetValue("PanGolin-Anti");
                if (string.IsNullOrEmpty(Anti))
                    Anti = "";
                bool flag = false;
                foreach (XSignInfo.mdata a in XSignInfo.data)
                    if (a.anti.Equals(Anti)) flag = true;
                if (!flag)
                {
                    AntiDialog(Base);
                    return;
                }
                switch (data.code)
                {
                    case 200:
                        mCard = data.data.code;
                        HttpApi.loginAsync(mResult, mCard, mac.GetMac());
                        break;
                    default:
                        if (File.Exists(Base.CacheDir.Path + "/card.dat"))
                        {
                            string card = File.ReadAllText(Base.CacheDir.Path + "/card.dat");
                            if (string.Empty != card)
                            {
                                mCard = card;
                                HttpApi.loginAsync(mResult, card, mac.GetMac());
                                return;
                            }
                        }
                        HttpApi.InitAsync(mResult);
                        break;
                }
            }

            //应用信息
            public void next(XAppInfo data)
            {
                if (data.code == 404)
                    System.Environment.Exit(0);
                appInfo = data;
                Loading.Hide();
                if (data.data.authmode == 0)
                    ShowDialog();
                else if (data.data.authmode == 1)
                {
                    share = PreferenceManager.GetDefaultSharedPreferences(Base);
                    edit = share.Edit();
                    int Show_Count = share.GetInt("Show_Count", 0);
                    if (Show_Count < appInfo.data.show_count)
                    {
                        new Handler().PostDelayed(() =>
                        {
                            ShowGroup();
                            edit.PutInt("Show_Count", Show_Count + 1);
                            edit.Apply();
                        }, data.data.delay_time * 1000);
                    }
                    else 
                        HookView.Visibility = ViewStates.Gone;
                }
            }

            //验证注册码
            public void next(XLoginInfo data)
            {
                Loading.Hide();
                switch (data.code)
                {
                    case 200:
                        StreamWriter streamWriter = File.CreateText(Base.CacheDir.Path + "/card.dat");
                        streamWriter.Write(mCard);
                        streamWriter.Close();
                        AntiRun = true;
                        Toast.MakeText(Base, data.msg + " 到期时间:" + data.time, ToastLength.Long).Show();
                        Check(data.token, true, mCard);
                        if (auth != null)
                            auth.Dismiss();
                        HookView.Visibility = ViewStates.Gone;
                        break;
                    default:
                        if (auth == null)
                        {
                            File.Delete(Base.CacheDir.Path + "/card.dat");
                            HttpApi.InitAsync(mResult);
                        }
                        else
                            Toast.MakeText(Base, data.msg, ToastLength.Long).Show();
                        break;
                }
            }

            //检测
            public void next(XCheckInfo data)
            {
                if (data.code == 404)
                {
                    Base.Finish();
                    System.Environment.Exit(0);
                }
            }

            //试用
            public void next(XTrialInfo data)
            {
                Loading.Hide();
                switch (data.code)
                {
                    case 200:
                        AntiRun = true;
                        Toast.MakeText(Base, data.msg + " 到期时间:" + data.time, ToastLength.Long).Show();
                        Check(data.token, false, "");
                        if (auth != null)
                            auth.Dismiss();
                        HookView.Visibility = ViewStates.Gone;
                        break;
                    default:
                        Toast.MakeText(Base, data.msg, ToastLength.Long).Show();
                        break;
                }
            }

            //查询
            public void next(XQueryInfo data)
            {
                switch (data.code)
                {
                    case 200:
                        new AlertDialog.Builder(Base, 4)
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
                        Toast.MakeText(Base, data.msg, ToastLength.Long).Show();
                        break;
                }
            }

            public void next(KfkPageData data)
            {
                Loading.Hide();
                RelativeLayout relativeLayout = new RelativeLayout(Base);
                View view = LayoutInflater.From(Base).Inflate(Base.Assets.OpenXmlResourceParser("res/layout/xamarin_pay.xml"), relativeLayout);
                CardView cardView = (CardView)view.FindViewWithTag("cardview");
                cardView.Radius = 45;
                cardView.Elevation = 1;
                cardView.PreventCornerOverlap = false;
                Spinner Goods = (Spinner)view.FindViewWithTag("goods");
                Goods.Adapter = new GoodsAdapter(Base, data.data);
                Spinner Products = (Spinner)view.FindViewWithTag("products");
                Button button = (Button)view.FindViewWithTag("Pay");
                AlertDialog pay = new AlertDialog.Builder(Base, 4)
                    .SetView(relativeLayout)
                    .Create();
                pay.Show();
                pay.Window.SetBackgroundDrawableResource(Android.Resource.Color.Transparent);
                Goods.ItemSelected += (v, i) =>
                {
                    Products.Adapter = new ProductAdapter(Base, data.data[i.Position].products);
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
                    PayType = new AlertDialog.Builder(Base, 5).SetTitle("请选择支付方式")
                    .SetSingleChoiceItems(item.ToArray(), -1, (dialog, pos) =>
                    {
                        //创建订单
                        Loading.Show(Base);
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
                        HttpApi.Build_order(this, data.data[Goods.SelectedItemPosition].products[Products.SelectedItemPosition].product_id, p, data.buyer_token, appInfo.data.weburl);
                        PayType.Dismiss();
                    })
                    .Show();
                };
            }

            public void next(Build_order_Info data)
            {
                Loading.Hide();
                if (data.data.paytype.Equals("QRCODE"))
                {
                    if (data.data.paysoft == 1 || data.data.paysoft == 3)
                    {
                        paydialog = new AlertDialog.Builder(Base, 5)
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
                            if (intent.ResolveActivity(Base.PackageManager) != null)
                            {
                                Base.StartActivity(intent);
                                return;
                            }
                            intent.SetData(Android.Net.Uri.Parse(url));
                            Base.StartActivity(intent);
                        }
                        else if (data.data.paysoft == 3)
                        {
                            WebView webView = new WebView(Base);
                            webView.Settings.JavaScriptEnabled = true;
                            webView.SetWebViewClient(new Web(webView, Base));
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
                        ImageView imageView = new ImageView(Base);
                        paydialog = new AlertDialog.Builder(Base, 5)
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
                    Toast.MakeText(Base, "暂时不支持该支付方式", ToastLength.Long).Show();
                }
                else
                    Toast.MakeText(Base, "暂时不支持该支付方式", ToastLength.Long).Show();

            }

            public void next(Card_Info data)
            {
                if (data.code == 200 && !string.IsNullOrEmpty(data.data.Result))
                {
                    EditText code = new EditText(Base);
                    code.Text = data.data.Result;
                    new AlertDialog.Builder(Base, 5)
                        .SetTitle("提卡成功")
                        .SetView(code)
                        .SetCancelable(false)
                        .SetNeutralButton("取消", delegate { })
                        .Show();
                    paydialog.Dismiss();
                }
            }
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
        private class Touch : Java.Lang.Object, View.IOnTouchListener
        {
            public bool OnTouch(View v, MotionEvent e)
            {
                if (e.Action == MotionEventActions.Down)
                {
                    ((TextView)v).SetTextColor(Color.Black);
                }
                if (e.Action == MotionEventActions.Up)
                {
                    ((TextView)v).SetTextColor(Color.ParseColor("#FFFFFF"));
                }
                return false;
            }
        }
        private static void ShowGroup()
        {
            if (appInfo.data.group_style == 0 || appInfo.data.group_style == 1)
            {
                RelativeLayout relativeLayout = new RelativeLayout(Base);
                view = LayoutInflater.From(Base).Inflate(Base.Assets.OpenXmlResourceParser("res/layout/xamarin_group_style_right.xml"), relativeLayout);
                auth = new AlertDialog.Builder(Base, 4)
                    .SetView(relativeLayout)
                    .SetCancelable(false)
                    .Show();
                auth.Window.SetBackgroundDrawableResource(Android.Resource.Color.Transparent);
                CardView cardView = (CardView)view.FindViewWithTag("view");
                cardView.Radius = 45;
                cardView.Elevation = 1;
                cardView.PreventCornerOverlap = false;
                GradientDrawable gd1 = new GradientDrawable();
                gd1.SetCornerRadius(45);
                gd1.SetColor(appInfo.data.group_style == 0 ? Color.White : Color.Black);
                cardView.Background = gd1;
                ImageView v = (ImageView)view.FindViewWithTag("image");
                HttpApi.LoadImage2("http://q2.qlogo.cn/headimg_dl?dst_uin=" + appInfo.data.qq_key + "&spec=100", v);
                if (appInfo.data.group_style == 1)
                {
                    ((TextView)view.FindViewWithTag("title")).SetTextColor(Color.White);
                    ((TextView)view.FindViewWithTag("msg")).SetTextColor(Color.White);
                    ((Button)view.FindViewWithTag("start")).SetTextColor(Color.White);
                    ((Button)view.FindViewWithTag("qq")).SetTextColor(Color.White);
                    ((Button)view.FindViewWithTag("more")).SetTextColor(Color.White);
                    ((Button)view.FindViewWithTag("group")).SetTextColor(Color.White);
                }
                ((TextView)view.FindViewWithTag("title")).SetText(appInfo.data.title, BufferType.Normal);
                ((TextView)view.FindViewWithTag("msg")).SetText(appInfo.data.notice, BufferType.Normal);
                if (share.GetInt("Share_Count", 0) >= appInfo.data.share_count) ((Button)view.FindViewWithTag("start")).SetText("进入软件", BufferType.Normal);
                view.FindViewWithTag("start").Click += (delegate
                {
                    if (share.GetInt("Share_Count", 0) >= appInfo.data.share_count)
                    {
                        if (auth != null)
                            auth.Dismiss();
                        HookView.Visibility = ViewStates.Gone;
                    }
                    else
                    {
                        Intent intent = new Intent("android.intent.action.SEND");
                        intent.SetType("text/plain");
                        intent.PutExtra(Intent.ExtraSubject, "分享");
                        intent.PutExtra(Intent.ExtraText, appInfo.data.share_msg);
                        intent.SetFlags(ActivityFlags.NewTask);
                        intent.SetComponent(new ComponentName("com.tencent.mobileqq", "com.tencent.mobileqq.activity.JumpActivity"));
                        Base.StartActivity(intent);
                        new Handler().PostDelayed(() =>
                        {
                            edit.PutInt("Share_Count", share.GetInt("Share_Count", 0) + 1);
                            edit.Apply();
                            Toast.MakeText(Base, "你已分享 " + share.GetInt("Share_Count", 0) + " / " + appInfo.data.share_count + "次", ToastLength.Long).Show();
                        }, 7000);
                    }

                });
                view.FindViewWithTag("group").Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse("mqqapi://card/show_pslcard?src_type=internal&version=1&uin=" + appInfo.data.group_key + "&card_type=group&source=qrcode"));
                    Base.StartActivity(browserIntent);
                });
                view.FindViewWithTag("qq").Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse("mqqwpa://im/chat?chat_type=wpa&uin=" + appInfo.data.qq_key));
                    Base.StartActivity(browserIntent);
                });
                view.FindViewWithTag("more").Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(appInfo.data.more_url));
                    Base.StartActivity(browserIntent);
                });
            }
            else if (appInfo.data.group_style == 2)
            {
                RelativeLayout relativeLayout = new RelativeLayout(Base);
                view = LayoutInflater.From(Base).Inflate(Base.Assets.OpenXmlResourceParser("res/layout/xamarin_group.xml"), relativeLayout);
                ((TextView)view.FindViewWithTag("Title")).SetText(appInfo.data.title, BufferType.Normal);
                ((TextView)view.FindViewWithTag("Tips")).SetText(appInfo.data.notice, BufferType.Normal);
                TextView Count = (TextView)view.FindViewWithTag("Count");
                Count.SetText("你已分享 " + share.GetInt("Share_Count", 0) + " / " + appInfo.data.share_count + "次", BufferType.Normal);
                GradientDrawable gd1 = new GradientDrawable();
                gd1.SetCornerRadius(100);
                gd1.SetColor(Color.ParseColor("#FF237686"));
                view.FindViewWithTag("Start1").Background = gd1;

                GradientDrawable gd = new GradientDrawable();
                gd.SetCornerRadius(50);
                gd.SetStroke(3, Color.White);
                view.FindViewWithTag("bottom").Background = gd;

                TextView Group = (TextView)view.FindViewWithTag("Group");
                TextView QQ = (TextView)view.FindViewWithTag("QQ");
                TextView More = (TextView)view.FindViewWithTag("More");
                if (share.GetInt("Share_Count", 0) >= appInfo.data.share_count) ((TextView)view.FindViewWithTag("Start")).SetText("进入软件", BufferType.Normal);
                view.FindViewWithTag("Start").Click += (delegate
                {
                    if (share.GetInt("Share_Count", 0) >= appInfo.data.share_count)
                    {
                        if (auth != null)
                            auth.Dismiss();
                        HookView.Visibility = ViewStates.Gone;
                    }
                    else
                    {
                        Intent intent = new Intent("android.intent.action.SEND");
                        intent.SetType("text/plain");
                        intent.PutExtra(Intent.ExtraSubject, "分享");
                        intent.PutExtra(Intent.ExtraText, appInfo.data.share_msg);
                        intent.SetFlags(ActivityFlags.NewTask);
                        intent.SetComponent(new ComponentName("com.tencent.mobileqq", "com.tencent.mobileqq.activity.JumpActivity"));
                        Base.StartActivity(intent);
                        new Handler().PostDelayed(() =>
                        {
                            edit.PutInt("Share_Count", share.GetInt("Share_Count", 0) + 1);
                            edit.Apply();
                            Count.SetText("你已分享 " + share.GetInt("Share_Count", 0) + " / " + appInfo.data.share_count + "次", BufferType.Normal);
                        }, 7000);
                    }

                });
                Group.Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse("mqqapi://card/show_pslcard?src_type=internal&version=1&uin=" + appInfo.data.group_key + "&card_type=group&source=qrcode"));
                    Base.StartActivity(browserIntent);
                });
                QQ.Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse("mqqwpa://im/chat?chat_type=wpa&uin=" + appInfo.data.qq_key));
                    Base.StartActivity(browserIntent);
                });
                More.Click += (delegate
                {
                    Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(appInfo.data.more_url));
                    Base.StartActivity(browserIntent);
                });
                Group.SetOnTouchListener(new Touch());
                QQ.SetOnTouchListener(new Touch());
                More.SetOnTouchListener(new Touch());
                RoundImageView v = (RoundImageView)view.FindViewWithTag("img");
                HttpApi.LoadImage("http://q2.qlogo.cn/headimg_dl?dst_uin=" + appInfo.data.qq_key + "&spec=100", v);
                auth = new AlertDialog.Builder(Base, 4)
                    .SetView(relativeLayout)
                    .SetCancelable(false)
                    .Show();
                auth.Window.SetBackgroundDrawableResource(Android.Resource.Color.Transparent);
                auth.Window.SetLayout(ViewGroup.LayoutParams.MatchParent, ViewGroup.LayoutParams.MatchParent);
            }
        }
        private static void ShowDialog()
        {
            if (appInfo.data.dialog_style == 0 || appInfo.data.dialog_style == 1)
            {
                RelativeLayout relativeLayout = new RelativeLayout(Base);
                relativeLayout.LayoutParameters = new ViewGroup.LayoutParams(ViewGroup.LayoutParams.MatchParent, ViewGroup.LayoutParams.WrapContent);
                EditText Card = new EditText(Base);
                Card.LayoutParameters = new ViewGroup.LayoutParams(ViewGroup.LayoutParams.MatchParent, ViewGroup.LayoutParams.WrapContent);
                relativeLayout.SetPadding(30, 0, 30, 0);
                relativeLayout.AddView(Card);
                if (appInfo.data.dialog_style == 1) Card.SetTextColor(Color.White);
                auth = new AlertDialog.Builder(Base, appInfo.data.dialog_style == 0 ? 5 : 4)
                    .SetTitle(appInfo.data.title)
                    .SetMessage(appInfo.data.notice)
                    .SetCancelable(false)
                    .SetView(relativeLayout)
                    .SetNegativeButton(string.IsNullOrEmpty(appInfo.data.weburl) ? "取消" : "购卡", delegate { })
                    .SetPositiveButton("验证", delegate { })
                    .SetNeutralButton(appInfo.data.try_count > 0 ? "试用" : "查码", delegate { })
                    .Show();
                auth.GetButton((int)DialogButtonType.Positive).Click += delegate
                {
                    string card = Card.Text.Trim();
                    if (string.Empty == card) return;
                    if (string.Empty == mac.GetMac())
                    {
                        new AlertDialog.Builder(Base)
                        .SetTitle("请确认你的权限是否打开")
                        .SetMessage("请在权限管理里(找到" + Base.PackageManager.GetApplicationLabel(Base.PackageManager.GetApplicationInfo(Base.PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                        .SetPositiveButton("去开启", (s, e) =>
                        {
                            Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                            Android.Net.Uri uri = Android.Net.Uri.FromParts("package", Base.PackageName, null);
                            intent.SetData(uri);
                            Base.StartActivity(intent);
                        })
                        .Show();
                        return;
                    }
                    Loading.Show(Base);
                    mCard = card;
                    HttpApi.loginAsync(mResult, card, mac.GetMac());
                };
                auth.GetButton((int)DialogButtonType.Negative).Click += delegate
                {
                    if (string.IsNullOrEmpty(appInfo.data.weburl))
                        System.Environment.Exit(0);
                    else
                    {
                        if (appInfo.data.weburl.StartsWith("https://www.kuaifaka.com/purchasing?link="))
                        {
                            Loading.Show(Base);
                            HttpApi.GetPage_data(mResult, appInfo.data.weburl);
                        }
                        else
                        {
                            Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(appInfo.data.weburl));
                            Base.StartActivity(browserIntent);
                        }
                    }
                };
                auth.GetButton((int)DialogButtonType.Neutral).Click += delegate
                {
                    if (appInfo.data.try_count > 0)
                    {
                        if (string.Empty == mac.GetMac())
                        {
                            new AlertDialog.Builder(Base)
                            .SetTitle("请确认你的权限是否打开")
                            .SetMessage("请在权限管理里(找到" + Base.PackageManager.GetApplicationLabel(Base.PackageManager.GetApplicationInfo(Base.PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                            .SetPositiveButton("去开启", (s, e) =>
                            {
                                Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                                Android.Net.Uri uri = Android.Net.Uri.FromParts("package", Base.PackageName, null);
                                intent.SetData(uri);
                                Base.StartActivity(intent);
                            })
                            .Show();
                            return;
                        }
                        Loading.Show(Base);
                        HttpApi.trialAsync(mResult, mac.GetMac());
                    }
                    else
                    {
                        string card = Card.Text.Trim();
                        if (string.Empty == card) return;
                        HttpApi.QueryAsync(mResult, card);
                    }
                };
            }
            else if (appInfo.data.dialog_style == 2)
            {
                RelativeLayout relativeLayout = new RelativeLayout(Base);
                view = LayoutInflater.From(Base).Inflate(Base.Assets.OpenXmlResourceParser("res/layout/xamarin_auth.xml"), relativeLayout);
                CardView cardView = (CardView)view.FindViewWithTag("cardview");
                cardView.Radius = 45;
                cardView.Elevation = 1;
                cardView.PreventCornerOverlap = false;
                RoundImageView v = (RoundImageView)view.FindViewWithTag("image");
                v.SetImageBitmap(BitmapFactory.DecodeStream(Base.Assets.Open("bj.png")));
                auth = new AlertDialog.Builder(Base, 4)
                   .SetView(relativeLayout)
                   .SetCancelable(false)
                   .Show();
                auth.Window.SetBackgroundDrawableResource(Android.Resource.Color.Transparent);
                EditText Card = (EditText)view.FindViewWithTag("et");
                ((TextView)view.FindViewWithTag("title")).SetText(appInfo.data.title, BufferType.Normal);
                ((TextView)view.FindViewWithTag("msg")).SetText(appInfo.data.notice, BufferType.Normal);
                if (string.Empty != appInfo.data.weburl)
                    ((Button)view.FindViewWithTag("bt2")).Visibility = ViewStates.Visible;
                if (appInfo.data.try_count > 0)
                    ((Button)view.FindViewWithTag("bt1")).SetText("试用", BufferType.Normal);
                else
                    ((Button)view.FindViewWithTag("bt1")).SetText("查码", BufferType.Normal);
                view.FindViewWithTag("bt1").Click += delegate
                {
                    if (appInfo.data.try_count > 0)
                    {
                        if (string.Empty == mac.GetMac())
                        {
                            new AlertDialog.Builder(Base)
                            .SetTitle("请确认你的权限是否打开")
                            .SetMessage("请在权限管理里(找到" + Base.PackageManager.GetApplicationLabel(Base.PackageManager.GetApplicationInfo(Base.PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                            .SetPositiveButton("去开启", (s, e) =>
                            {
                                Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                                Android.Net.Uri uri = Android.Net.Uri.FromParts("package", Base.PackageName, null);
                                intent.SetData(uri);
                                Base.StartActivity(intent);
                            })
                            .Show();
                            return;
                        }
                        Loading.Show(Base);
                        HttpApi.trialAsync(mResult, mac.GetMac());
                    }
                    else
                    {
                        string card = Card.Text.Trim();
                        if (string.Empty == card) return;
                        HttpApi.QueryAsync(mResult, card);
                    }
                };
                view.FindViewWithTag("bt2").Click += delegate
                {
                    if (appInfo.data.weburl.StartsWith("https://www.kuaifaka.com/purchasing?link="))
                    {
                        Loading.Show(Base);
                        HttpApi.GetPage_data(mResult, appInfo.data.weburl);
                    }
                    else
                    {
                        Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(appInfo.data.weburl));
                        Base.StartActivity(browserIntent);
                    }
                };
                view.FindViewWithTag("bt3").Click += delegate
                {
                    string card = Card.Text.Trim();
                    if (string.Empty == card) return;
                    if (string.Empty == mac.GetMac())
                    {
                        new AlertDialog.Builder(Base)
                        .SetTitle("请确认你的权限是否打开")
                        .SetMessage("请在权限管理里(找到" + Base.PackageManager.GetApplicationLabel(Base.PackageManager.GetApplicationInfo(Base.PackageName, 0)) + ")打开-读取手机信息权限，如果依旧没有解决请联系客服进行协助")
                        .SetPositiveButton("去开启", (s, e) =>
                        {
                            Intent intent = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
                            Android.Net.Uri uri = Android.Net.Uri.FromParts("package", Base.PackageName, null);
                            intent.SetData(uri);
                            Base.StartActivity(intent);
                        })
                        .Show();
                        return;
                    }
                    Loading.Show(Base);
                    mCard = card;
                    HttpApi.loginAsync(mResult, card, mac.GetMac());
                };
            }
            if (appInfo.data.version > Convert.ToInt32(Properties.GetProperty("Ver")))
            {
                new AlertDialog.Builder(Base)
                               .SetTitle(appInfo.data.title)
                               .SetMessage(appInfo.data.update_msg)
                               .SetCancelable(appInfo.data.updatemode == 0 ? false : true)
                               .SetPositiveButton("更新", (s, e) =>
                               {
                                   Intent browserIntent = new Intent(Intent.ActionDefault, Android.Net.Uri.Parse(appInfo.data.update_url));
                                   Base.StartActivity(browserIntent);
                               }).Show();
            }
        }
        private static void Check(string Token, bool CheckType, string card)
        {
            Handler handler = new Handler();
            run = new Java.Lang.Runnable(() =>
            {
                HttpApi.CheckAsync(mResult, Token, CheckType, mac.GetMac(), card);
                handler.PostDelayed(run, 1000 * 20);
            });
            handler.Post(run);
        }
        private static void AntiDialog(Activity activity)
        {
            new AlertDialog.Builder(activity)
               .SetTitle("Piracy warning")
               .SetMessage(XSignInfo.msg)
               .SetCancelable(false)
               .SetPositiveButton("Close", (s, e) =>
               {
                   Base.Finish();
                   System.Environment.Exit(0);
               })
                .Show();
            appInfo = null;
        }
    }
}