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
using Java.Lang;

namespace AX_Inject.AuthDialog.api.KfkModel
{
    public class KfkPageData : XBasics
    {
        public bool WxPay { get; set; }
        public bool ZfbPay { get; set; }
        public bool QQPay { get; set; }
        public string buyer_token { get; set; }
        public List<mdata> data { get; set; }
        public class mdata
        {
            public string name { get; set; }
            public int cate_id { get; set; }

            public List<mproducts> products { get; set; }

            public class mproducts
            {
                public string name { get; set; }

                public int product_id { get; set; }
            }
        }
    }
}