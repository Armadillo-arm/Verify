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

namespace AX_Inject.AuthDialog.model
{
    public class XCodeInfo : XBasics
    {
        public datainfo data { get; set; }
        public class datainfo
        {
            public string code { get; set; }
        }
    }
}