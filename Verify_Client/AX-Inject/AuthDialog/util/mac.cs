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
using Android.Telephony;

namespace AX_Inject.AuthDialog.util
{
    public class mac
    {
        public static string GetMac()
        {
            try
            {
                if (Build.VERSION.SdkInt >= BuildVersionCodes.O)
                {
                    return Build.GetSerial();
                }
                else
                    return Build.Serial;
            }
            catch (Exception)
            { }
            return "";
        }
    }
}