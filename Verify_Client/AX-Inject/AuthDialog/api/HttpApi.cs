using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.OS;
using Android.Runtime;
using Android.Views;
using Android.Widget;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using AX_Inject.AuthDialog.util;
using Android.Util;
using Java.Net;
using AX_Inject.AuthDialog.model;
using Java.IO;
using AX_Inject.AuthDialog.view;
using Android.Graphics;
using Android.Graphics.Drawables;
using System.Net;
using System.IO;
using AX_Inject.AuthDialog.api.KfkModel;

namespace AX_Inject.AuthDialog.api
{
    public class HttpApi
    {
        public interface Result<T> where T : XBasics
        {
            void next(T data);
        }
        /*private static Request GetRequest(Dictionary<string, string> Map)
        {
            Request.Builder request = new Request.Builder()
                 .Url("http://49.234.28.227:99/Auth/Verify")
                 .AddHeader("Appid", Dialog.Properties.GetProperty("Appid"))
                 .AddHeader("Ver", Dialog.Properties.GetProperty("Ver", "1"))
                 .AddHeader("Key", "" + new DateTimeOffset(DateTime.UtcNow).ToUnixTimeSeconds());
            foreach (KeyValuePair<string, string> keys in Map)
                request.AddHeader(keys.Key, keys.Value);
            return request.Build();
        }*/
        private static HttpWebRequest GetOkHttpClient(Dictionary<string, string> Map)
        {
            HttpWebRequest Request = HttpWebRequest.CreateHttp(new Uri("http://y.yssgos.com/Auth/Verify"));
            Request.Headers.Add("Appid", Dialog.Properties.GetProperty("Appid"));
            Request.Headers.Add("Ver", Dialog.Properties.GetProperty("Ver", "1"));
            Request.Headers.Add("Key", "" + new DateTimeOffset(DateTime.UtcNow).ToUnixTimeSeconds());
            foreach (KeyValuePair<string, string> keys in Map)
                Request.Headers.Add(keys.Key, keys.Value);
            return Request;
        }
        //初始化
        public static async void InitAsync(Result<XAppInfo> call)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_GetSoftInfo");
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<XAppInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
        //登录
        public static async void loginAsync(Result<XLoginInfo> call, string card, string mac)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Verify");
            keys.Add("Mac", mac);
            keys.Add("Code", card);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<XLoginInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
        //试用
        public static async void trialAsync(Result<XTrialInfo> call, string mac)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Trial");
            keys.Add("Mac", mac);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<XTrialInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
        //机器码取注册码
        public static async void GetCardAsync(Result<XCodeInfo> call, string mac)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_GetCode");
            keys.Add("Mac", mac);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                Dialog.XSignInfo = JsonConvert.DeserializeObject<XSignInfo>(Des.DesDecrypt(response.Headers.Get("Sign"), md5.GetKey(request.Headers.Get("Key"))));
                call.next(JsonConvert.DeserializeObject<XCodeInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
        //校验
        public static async void CheckAsync(Result<XCheckInfo> call, string Token, bool CheckType, string mac, string card)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Check");
            keys.Add("Mac", mac);
            keys.Add("Token", Token);
            keys.Add("Code", card);
            keys.Add("Type", CheckType ? "formal" : "trial");
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<XCheckInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
        //查码
        public static async void QueryAsync(Result<XQueryInfo> call, string card)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Query");
            keys.Add("Code", card);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<XQueryInfo>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }

        public static async void LoadImage(string url, RoundImageView img)
        {
            WebRequest Request = HttpWebRequest.CreateHttp(url);
            using (WebResponse response = Request.GetResponse())
            {
                using (Stream stream = response.GetResponseStream())
                {
                    Bitmap bitmap = await BitmapFactory.DecodeStreamAsync(stream);
                    img.SetImageBitmap(bitmap);
                    img.play();
                }
            }
        }
        public static async void LoadImage2(string url, ImageView img)
        {
            WebRequest Request = HttpWebRequest.CreateHttp(url);
            using (WebResponse response = Request.GetResponse())
            {
                using (Stream stream = response.GetResponseStream())
                {
                    Bitmap bitmap = await BitmapFactory.DecodeStreamAsync(stream);
                    img.SetImageBitmap(bitmap);
                }
            }
        }

        //获取商品信息
        public static async void GetPage_data(Result<KfkPageData> call, string kfk)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_GetPage_data");
            keys.Add("Url", kfk);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<KfkPageData>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }

        //创建订单
        public static async void Build_order(Result<Build_order_Info> call, int Productid, int Paytype, string Buyertoken, string kfk)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Build_order");
            keys.Add("Productid", "" + Productid);
            keys.Add("Paytype", "" + Paytype);
            keys.Add("Buyertoken", Buyertoken);
            keys.Add("Url", kfk);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<Build_order_Info>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }

        //监听支付
        public static async void Get_order_state(Result<Card_Info> call, string order_num)
        {
            Dictionary<string, string> keys = new Dictionary<string, string>();
            keys.Add("Api", "PanGolin_Get_order_state");
            keys.Add("Order", order_num);
            HttpWebRequest request = GetOkHttpClient(keys);
            HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync();
            if (response.StatusCode == HttpStatusCode.OK)
            {
                string body = response.Headers.Get("Result");
                call.next(JsonConvert.DeserializeObject<Card_Info>(Des.DesDecrypt(body, md5.GetKey(request.Headers.Get("Key")))));
            }
        }
    }
}