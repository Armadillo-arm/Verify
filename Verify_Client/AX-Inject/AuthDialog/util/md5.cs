using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Cryptography;
using System.Text;

using Android.App;
using Android.Content;
using Android.OS;
using Android.Runtime;
using Android.Views;
using Android.Widget;

namespace AX_Inject.AuthDialog.util
{
    public class md5
    {
        public static string GetKey(string key)
        {
            return GetMd5(GetMd5(GetMd5(key))).Substring(0, 8);
        }
        public static string GetMd5(string input)
        {
            MD5 d5 = MD5.Create();
            byte[] data = d5.ComputeHash(Encoding.UTF8.GetBytes(input));
            StringBuilder sBuilder = new StringBuilder();
            for (int i = 0; i < data.Length; i++)
            {
                sBuilder.Append(data[i].ToString("x2"));
            }
            return sBuilder.ToString();
        }
    }
}