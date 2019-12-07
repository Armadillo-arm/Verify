using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.Graphics;
using Android.Graphics.Drawables;
using Android.OS;
using Android.Runtime;
using Android.Util;
using Android.Views;
using Android.Widget;

namespace AX_Inject.AuthDialog.view
{
    public class BorderButton : Button
    {
        public BorderButton(Context context) : base(context)
        {
            init();
        }

        public BorderButton(Context context, IAttributeSet attrs) : base(context, attrs)
        {
            init();
        }

        public BorderButton(Context context, IAttributeSet attrs, int defStyleAttr) : base(context, attrs, defStyleAttr)
        {
            init();
        }

        public BorderButton(Context context, IAttributeSet attrs, int defStyleAttr, int defStyleRes) : base(context, attrs, defStyleAttr, defStyleRes)
        {
            init();
        }

        protected BorderButton(IntPtr javaReference, JniHandleOwnership transfer) : base(javaReference, transfer)
        {
        }
        private void init()
        {
            SetSingleLine(true);
            SetTextColor(Color.White);
        }

        protected override void OnDraw(Canvas canvas)
        {
            base.OnDraw(canvas);
            GradientDrawable gd = new GradientDrawable();
            gd.SetCornerRadius(45);
            SetTextColor(Color.Black);
            //gd.SetColor(Color.ParseColor("#FF962CCE"));
            //gd.SetStroke(5, Color.ParseColor("#FF962CCE"));
            Background = gd;
        }
    }
}