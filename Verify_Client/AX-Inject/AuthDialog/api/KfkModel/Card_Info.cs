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
    public class Card_Info : XBasics
    {
        public mdata data { get; set; }
        public class mdata
        {
            public string Result { get; set; }
        }
    }
}