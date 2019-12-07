using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Resources;
using System.Text;

using Android.App;
using Android.Content;
using Android.Content.Res;
using Android.OS;
using Android.Runtime;
using Android.Util;
using Android.Views;
using Android.Widget;

namespace AX_Inject.AuthDialog.util
{
    public class Dp2Px
    {
        public static int dp2px(float dpVal)
        {
            int i= (int)TypedValue.ApplyDimension(ComplexUnitType.Dip,
                                                   dpVal,Resources.System.DisplayMetrics);
            return i;
        }

    }
}