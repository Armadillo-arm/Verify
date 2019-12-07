using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.Graphics;
using Android.OS;
using Android.Runtime;
using Android.Views;
using Android.Widget;
using AX_Inject.AuthDialog.util;

namespace AX_Inject.AuthDialog.view
{
    public class Wave
    {
        public Path path;          //水波路径
        public int width;          //画布宽度（2倍波长）
        public int wave;           //波幅（振幅）
        public float offsetX;        //水波的水平偏移量
        public float offsetY;        //水波的竖直偏移量
        public float velocity;       //水波移动速度（像素/秒）
        private float scaleX;       //水平拉伸比例
        private float scaleY;       //竖直拉伸比例
        public Wave(/*Context context, */int offsetX, int offsetY, int velocity, float scaleX, float scaleY, int w, int h, int wave)
        {
            this.width = (int)(2 * scaleX * w); //画布宽度（2倍波长）
            this.wave = wave;           //波幅（波宽）
            this.scaleX = scaleX;       //水平拉伸量
            this.scaleY = scaleY;       //竖直拉伸量
            this.offsetX = offsetX;     //水平偏移量
            this.offsetY = offsetY;     //竖直偏移量
            this.velocity = velocity;   //移动速度（像素/秒）
            this.path = buildWavePath(width, h);
        }
        public void updateWavePath(int w, int h, int waveHeight)
        {
            this.wave = (wave > 0) ? wave : waveHeight / 2;
            this.width = (int)(2 * scaleX * w);  //画布宽度（2倍波长）
            this.path = buildWavePath(width, h);
        }


        private Path buildWavePath(int width, int height)
        {
            int DP = Dp2Px.dp2px(1);//一个dp在当前设备表示的像素量（水波的绘制精度设为一个dp单位）
            if (DP < 1)
            {
                DP = 1;
            }

            int wave = (int)(scaleY * this.wave);//计算拉伸之后的波幅

            Path path = new Path();
            path.MoveTo(0, 0);
            path.LineTo(0, height - wave);

            for (int x = DP; x < width; x += DP)
            {
                path.LineTo(x, height - wave - wave * (float)Math.Sin(4.0 * Math.PI * x / width));
            }

            path.LineTo(width, height - wave);
            path.LineTo(width, 0);
            path.Close();
            return path;
        }

    }
}