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
    public class XAppInfo : XBasics
    {
        public mdata data { get; set; }
        public class mdata
        {
            public int authmode { get; set; }

            public string title { get; set; }

            public string notice { get; set; }

            public string weburl { get; set; }

            public int try_count { get; set; }

            public int version { get; set; }

            public int updatemode { get; set; }

            public string update_msg { get; set; }

            public string update_url { get; set; }

            public int share_count { get; set; }

            public string share_msg { get; set; }

            public int delay_time { get; set; }

            public int show_count { get; set; }

            public string more_url { get; set; }

            public Int32 qq_key { get; set; }

            public Int32 group_key { get; set; }

            public int dialog_style { get; set; }

            public int group_style { get; set; }
        }
    }
}