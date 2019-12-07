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
    public class BorderEditText : EditText
    {
        public BorderEditText(Context context) : base(context)
        {
        }

        public BorderEditText(Context context, IAttributeSet attrs) : base(context, attrs)
        {
            Init();
        }

        public BorderEditText(Context context, IAttributeSet attrs, int defStyleAttr) : base(context, attrs, defStyleAttr)
        {
            Init();
        }

        public BorderEditText(Context context, IAttributeSet attrs, int defStyleAttr, int defStyleRes) : base(context, attrs, defStyleAttr, defStyleRes)
        {
            Init();
        }

        protected BorderEditText(IntPtr javaReference, JniHandleOwnership transfer) : base(javaReference, transfer)
        {
            Init();
        }
        private void Init()
        {
            SetSingleLine(true);
            SetTextColor(Color.Red);
            SetHintTextColor(Color.Black);
            Gravity = GravityFlags.Center;
        }
        protected override void OnDraw(Canvas canvas)
        {
            base.OnDraw(canvas);
            GradientDrawable gd = new GradientDrawable();
            gd.SetCornerRadius(45);
            gd.SetStroke(5, Color.ParseColor("#FF962CCE"));
            Background = gd;
        }
    }
}