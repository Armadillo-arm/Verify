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
using AX_Inject.AuthDialog.util;
using Java.Lang;

namespace AX_Inject.AuthDialog.view
{
    public class MultiWaveHeader : ViewGroup
    {

        private Paint mPaint = new Paint();
        private Matrix mMatrix = new Matrix();
        private List<Wave> mltWave = new List<Wave>();
        private int mWaveHeight;
        private int mStartColor;
        private int mCloseColor;
        private int mGradientAngle;
        private bool mIsRunning;
        private float mVelocity;
        private float mColorAlpha;
        private float mProgress;
        private long mLastTime = 0;

        public MultiWaveHeader(Context context) : base(context)
        {
        }

        public MultiWaveHeader(IntPtr javaReference, JniHandleOwnership transfer) : base(javaReference, transfer)
        {
        }

        public MultiWaveHeader(Context context, IAttributeSet attrs) : base(context, attrs)
        {
            Init();
        }

        public MultiWaveHeader(Context context, IAttributeSet attrs, int defStyleAttr) : base(context, attrs, defStyleAttr)
        {
        }

        public MultiWaveHeader(Context context, IAttributeSet attrs, int defStyleAttr, int defStyleRes) : base(context, attrs, defStyleAttr, defStyleRes)
        {

        }

        private void Init()
        {
            mPaint.AntiAlias = true;
            mWaveHeight = Dp2Px.dp2px(50);
            Random random = new Random();
            int ranColor = Color.Red | random.Next(0x00ffffff);
            mStartColor = ranColor;
            mCloseColor = ranColor - 500;
            mColorAlpha = 0.4f;
            mProgress = 1f;
            mVelocity = 1f;
            mGradientAngle = 45;
            mIsRunning = true;

            Tag = "70,25,1.4,1.4,-26\n" +
                   "100,5,1.4,1.2,15\n" +
                   "420,0,1.15,1,-10\n" +
                   "520,10,1.7,1.5,20\n" +
                   "220,0,1,1,-15";
        }
        public  long currentTimeMillis()
        {
            return (long)((DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0, DateTimeKind.Utc)).TotalMilliseconds);
        }
        protected override void DispatchDraw(Canvas canvas)
        {
            base.DispatchDraw(canvas);
            int height = Height;
            long thisTime = currentTimeMillis();
            foreach (Wave wave in mltWave)
            {
                mMatrix.Reset();
                canvas.Save();
                if (mLastTime > 0 && wave.velocity != 0)
                {
                    float offsetX = (wave.offsetX - (wave.velocity * mVelocity * (thisTime - mLastTime) / 1000f));
                    if (-wave.velocity > 0)
                    {
                        offsetX %= wave.width / 2;
                    }
                    else
                    {
                        while (offsetX < 0)
                        {
                            offsetX += (wave.width / 2);
                        }
                    }
                    wave.offsetX = offsetX;
                    mMatrix.SetTranslate(offsetX, (1 - mProgress) * height);//wave.offsetX =
                    canvas.Translate(-offsetX, -wave.offsetY - (1 - mProgress) * height);
                }
                else
                {
                    mMatrix.SetTranslate(wave.offsetX, (1 - mProgress) * height);
                    canvas.Translate(-wave.offsetX, -wave.offsetY - (1 - mProgress) * height);
                }
                mPaint.Shader.SetLocalMatrix(mMatrix);
                canvas.DrawPath(wave.path, mPaint);
                canvas.Restore();
            }
            mLastTime = thisTime;
            if (mIsRunning)
            {
                Invalidate();
            }

        }

        protected override void OnLayout(bool changed, int l, int t, int r, int b)
        {
        }

        protected override void OnSizeChanged(int w, int h, int oldw, int oldh)
        {
            base.OnSizeChanged(w, h, oldw, oldh);
            updateWavePath(w, h);
            updateLinearGradient(w, h);
        }
        private int AlphaComponent(int i, int i2)
        {
            if (i2 >= 0 && i2 <= 255)
            {
                return (16777215 & i) | (i2 << 24);
            }
            return Color.Purple;
        }
        private void updateLinearGradient(int width, int height)
        {
            int startColor = AlphaComponent(mStartColor, (int)(mColorAlpha * 255));
            int closeColor = AlphaComponent(mCloseColor, (int)(mColorAlpha * 255));
            double w = width;
            double h = height * mProgress;
            double r = Java.Lang.Math.Sqrt(w * w + h * h) / 2;
            double y = r * Java.Lang.Math.Sin(2 * Java.Lang.Math.Pi * mGradientAngle / 360);
            double x = r * Java.Lang.Math.Cos(2 * Java.Lang.Math.Pi * mGradientAngle / 360);
            mPaint.SetShader(new LinearGradient((int)(w / 2 - x), (int)(h / 2 - y), (int)(w / 2 + x), (int)(h / 2 + y), new Color(startColor), new Color(closeColor), Shader.TileMode.Clamp));
        }

        private void updateWavePath(int w, int h)
        {
            mltWave.Clear();
            string[] waves = Tag.ToString().Split("\n");
            if ("-1".Equals(Tag))
            {
                waves = "70,25,1.4,1.4,-26\n100,5,1.4,1.2,15\n420,0,1.15,1,-10\n520,10,1.7,1.5,20\n220,0,1,1,-15".Split("\n");
            }
            else if ("-2".Equals(Tag))
            {
                waves = "0,0,1,0.5,90\n90,0,1,0.5,90".Split("\n");
            }

            foreach (string wave in waves)
            {
                string[] args = wave.Split(",");
                if (args.Length == 5)
                {
                    mltWave.Add(new Wave(Dp2Px.dp2px(Float.ParseFloat(args[0])), Dp2Px.dp2px(Float.ParseFloat(args[1])), Dp2Px.dp2px(Float.ParseFloat(args[4])), Float.ParseFloat(args[2]), Float.ParseFloat(args[3]), w, h, mWaveHeight / 2));
                }
            }


        }
    }

}