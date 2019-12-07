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
    public class XTrialInfo:XBasics
    {
        public string token { get; set; }

        public string time { get; set; }
    }
}