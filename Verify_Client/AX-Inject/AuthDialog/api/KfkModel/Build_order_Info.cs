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
using AX_Inject.AuthDialog.model;

namespace AX_Inject.AuthDialog.api.KfkModel
{
    public class Build_order_Info : XBasics
    {
        public mdata data { get; set; }
        public class mdata
        {
            public string payurl { get; set; }

            public string paytype { get; set; }

            public string order_num { get; set; }

            public int paysoft { get; set; }
        }
    }
}