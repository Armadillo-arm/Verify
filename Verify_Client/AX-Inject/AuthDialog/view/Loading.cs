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

namespace AX_Inject.AuthDialog.view
{
    public class Loading
    {
        private static AlertDialog progressDialog;
        public static void Show(Activity activity)
        {
            if (progressDialog != null) progressDialog.Dismiss();
            progressDialog = new AlertDialog.Builder(activity)
                .SetMessage("加载中......")
                .SetCancelable(false)
                .Show();
        }
        public static void Hide()
        {
            if (progressDialog != null) progressDialog.Dismiss();
        }
    }
}