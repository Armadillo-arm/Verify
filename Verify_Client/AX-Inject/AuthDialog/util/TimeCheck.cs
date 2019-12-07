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

namespace AX_Inject.AuthDialog.util
{
    public class TimeCheck
    {
        public static void Check(long Time1,long Time2)
        {
            if(Time1!=Time2)
                System.Environment.Exit(0);
        }
    }
}