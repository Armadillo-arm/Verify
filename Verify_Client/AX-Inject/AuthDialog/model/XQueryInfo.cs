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
    public class XQueryInfo:XBasics
    {
        public datainfo data { get; set; }
        public class datainfo
        {
            public string code { get; set; }

            public int use_count { get; set; }

            public int frozen { get; set; }

            public string time_str { get; set; }

            public int overdue { get; set; }

            public string computer_uid { get; set; }
        }
    }
}