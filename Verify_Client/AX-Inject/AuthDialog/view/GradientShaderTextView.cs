using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.Graphics;
using Android.OS;
using Android.Runtime;
using Android.Util;
using Android.Views;
using Android.Widget;

namespace AX_Inject.AuthDialog.view
{
    public class GradientShaderTextView : TextView
    {
        private int mViewWidth;
        private Paint mPaint;
        private LinearGradient mLinearGradient;
        private Matrix mGrandientMatrix;
        private int mTranslate;
        public GradientShaderTextView(Context context) : base(context)
        {
        }

        public GradientShaderTextView(Context context, IAttributeSet attrs) : base(context, attrs)
        {
        }

        public GradientShaderTextView(Context context, IAttributeSet attrs, int defStyleAttr) : base(context, attrs, defStyleAttr)
        {
        }

        public GradientShaderTextView(Context context, IAttributeSet attrs, int defStyleAttr, int defStyleRes) : base(context, attrs, defStyleAttr, defStyleRes)
        {
        }

        protected GradientShaderTextView(IntPtr javaReference, JniHandleOwnership transfer) : base(javaReference, transfer)
        {
        }

        protected override void OnDraw(Canvas canvas)
        {
            base.OnDraw(canvas);
            if (mGrandientMatrix != null)
            {
                mTranslate += mViewWidth / 5;
                if (mTranslate > 2 * mViewWidth)
                {
                    mTranslate = -mViewWidth;
                }
                mGrandientMatrix.SetTranslate(mTranslate, 0);
                mLinearGradient.SetLocalMatrix(mGrandientMatrix);
                PostInvalidateDelayed(30);
            }
        }

        protected override void OnSizeChanged(int w, int h, int oldw, int oldh)
        {
            base.OnSizeChanged(w, h, oldw, oldh);
            if (mViewWidth == 0)
            {
                mViewWidth = MeasuredWidth;
                if (mViewWidth > 0)
                {
                    mPaint = Paint;
                    mLinearGradient = new LinearGradient(0, 0, mViewWidth, 0, new int[] { Color.Yellow, Color.Cyan, Color.Yellow }, null, Shader.TileMode.Clamp);
                    mPaint.SetShader(mLinearGradient);
                    mGrandientMatrix = new Matrix();
                }
            }
        }
    }
}