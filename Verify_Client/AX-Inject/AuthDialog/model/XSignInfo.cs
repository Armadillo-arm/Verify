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
    public class XSignInfo
    {
        public string msg { get; set; }

        public List<mdata> data { get; set; }

        public class mdata
        {
            public string anti { get; set; }
        }
    }
}