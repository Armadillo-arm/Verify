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
using Android.Views.Animations;
using Android.Widget;
using static Android.Graphics.Paint;
using static Android.Graphics.PorterDuff;

namespace AX_Inject.AuthDialog.view
{
    public class RoundImageView : ImageView
    {
        public enum RoundMode
        {
            ROUND_VIEW, ROUND_DRAWABLE
        }
        private static int DEFAULT_BORDER_WIDTH = 0;
        private static int DEFAULT_BORDER_COLOR = Color.Transparent;
        private static int DEFAULT_FILL_COLOR = Color.Transparent;
        private bool roundDisable=false;
        private RoundMode roundMode = RoundMode.ROUND_DRAWABLE;
        private int borderColor = DEFAULT_BORDER_COLOR;
        private int borderWidth = DEFAULT_BORDER_WIDTH;
        private int fillColor = DEFAULT_FILL_COLOR;
        private Paint borderPaint;
        private Paint fillPaint;
        private Paint imagePaint;
        private Paint portPaint;
        private Rect bounds = new Rect();
        private float radius = 0;
        private float cx = 0;
        private float cy = 0;

        protected override void OnDraw(Canvas canvas)
        {
            if (roundDisable)
            {
                base.OnDraw(canvas);
                return;
            }
            if (Drawable == null && roundMode == RoundMode.ROUND_DRAWABLE)
            {
                base.OnDraw(canvas);
                return;
            }
            computeRoundBounds();
            drawCircle(canvas);
            drawImage(canvas);
            play();
        }
       public void play()
        {
            RotateAnimation rotate = new RotateAnimation(0f, 360f, Dimension.RelativeToSelf, 0.5f, Dimension.RelativeToSelf, 0.5f);
            LinearInterpolator lin = new LinearInterpolator();
            rotate.Interpolator = lin;
            rotate.Duration = 2000;
            rotate.RepeatCount = -1;
            rotate.FillAfter = true;
            rotate.StartOffset = 10;
            Animation = rotate;
        }

        private void computeRoundBounds()
        {
            if (roundMode == RoundMode.ROUND_VIEW)
            {
                bounds.Left = PaddingLeft;
                bounds.Top = PaddingTop;
                bounds.Right = Width - PaddingRight;
                bounds.Bottom = Height - PaddingBottom;
            }
            else if (roundMode == RoundMode.ROUND_DRAWABLE)
            {
                Drawable.CopyBounds(bounds);
            }

            radius = Math.Min(bounds.Width(), bounds.Height()) / 2f;
            cx = bounds.Left + bounds.Width() / 2f;
            cy = bounds.Top + bounds.Height() / 2f;
        }
        private void drawCircle(Canvas canvas)
        {
            int saveCount = canvas.SaveCount;
            canvas.Save();
            adjustCanvas(canvas);

            canvas.DrawCircle(cx, cy, radius, fillPaint);
            if (borderWidth > 0)
            {
                canvas.DrawCircle(cx, cy, radius - borderWidth / 2f, borderPaint);
            }

            canvas.RestoreToCount(saveCount);

        }
        private void adjustCanvas(Canvas canvas)
        {
            if (roundMode == RoundMode.ROUND_DRAWABLE)
            {
                if (Build.VERSION.SdkInt >=BuildVersionCodes.JellyBean)
                {
                    if (CropToPadding)
                    {
                        int scrollX = ScrollX;
                        int scrollY = ScrollY;
                        canvas.ClipRect(scrollX + PaddingLeft, scrollY + PaddingTop,
                                        scrollX + Right - Left - PaddingRight,
                                        scrollY + Bottom - Top - PaddingBottom);
                    }
                }

                canvas.Translate(PaddingLeft, PaddingTop);
                if (ImageMatrix != null)
                {
                    Matrix m = new Matrix(ImageMatrix);
                    canvas.Concat(m);
                }
            }
        }
        private void drawImage(Canvas canvas)
        {
            Bitmap src = Bitmap.CreateBitmap(Width, Height, Bitmap.Config.Argb4444);
            base.OnDraw(new Canvas(src));

            Bitmap port = Bitmap.CreateBitmap(Width, Height, Bitmap.Config.Argb4444);
            Canvas portCanvas = new Canvas(port);

            int saveCount = portCanvas.SaveCount;
            portCanvas.Save();
            adjustCanvas(portCanvas);
            portCanvas.DrawCircle(cx, cy, radius, portPaint);
            portCanvas.RestoreToCount(saveCount);

            portCanvas.DrawBitmap(src, 0, 0, imagePaint);
            src.Recycle();

            canvas.DrawBitmap(port, 0, 0, null);
            port.Recycle();

        }


        public RoundImageView(Context context) : base(context)
        {
            initView();
        }
        public RoundImageView(Context context, IAttributeSet attrs) : base(context, attrs)
        {
            initView();
        }
        public RoundImageView(Context context, IAttributeSet attrs, int defStyleAttr) : base(context, attrs, defStyleAttr)
        {
            initView();
        }

        public RoundImageView(Context context, IAttributeSet attrs, int defStyleAttr, int defStyleRes) : base(context, attrs, defStyleAttr, defStyleRes)
        {
            initView();
        }
        private void initView()
        {
            portPaint = new Paint();
            portPaint.AntiAlias = true;


            borderPaint = new Paint();
            borderPaint.AntiAlias = true;
            borderPaint.Color = Color.Transparent;
            borderPaint.StrokeWidth = 0;
            borderPaint.SetStyle(Style.Stroke);

            fillPaint = new Paint();
            fillPaint.AntiAlias = true;
            fillPaint.Color = Color.Transparent;
            fillPaint.SetStyle(Style.Fill);

            imagePaint = new Paint();
            imagePaint.AntiAlias = true;
            imagePaint.SetXfermode(new PorterDuffXfermode(Mode.SrcIn));
        }

    }
}