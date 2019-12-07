package pangolin.inject.XUtil;

import androidx.viewpager.widget.ViewPager;

import com.youth.banner.Transformer;
import com.youth.banner.transformer.AccordionTransformer;
import com.youth.banner.transformer.BackgroundToForegroundTransformer;
import com.youth.banner.transformer.CubeInTransformer;
import com.youth.banner.transformer.CubeOutTransformer;
import com.youth.banner.transformer.DefaultTransformer;
import com.youth.banner.transformer.DepthPageTransformer;
import com.youth.banner.transformer.FlipHorizontalTransformer;
import com.youth.banner.transformer.FlipVerticalTransformer;
import com.youth.banner.transformer.ForegroundToBackgroundTransformer;
import com.youth.banner.transformer.RotateDownTransformer;
import com.youth.banner.transformer.RotateUpTransformer;
import com.youth.banner.transformer.ScaleInOutTransformer;
import com.youth.banner.transformer.StackTransformer;
import com.youth.banner.transformer.TabletTransformer;
import com.youth.banner.transformer.ZoomInTransformer;
import com.youth.banner.transformer.ZoomOutSlideTransformer;
import com.youth.banner.transformer.ZoomOutTranformer;

import java.util.List;
import java.util.Random;

public class XTransformerRandom {
    public static Class<? extends ViewPager.PageTransformer> RandomTransformer() {
        Random random = new Random();
        switch (random.nextInt(17) + 1) {
            case 1:
                return DefaultTransformer.class;
            case 2:
                return AccordionTransformer.class;
            case 3:
                return BackgroundToForegroundTransformer.class;
            case 4:
                return ForegroundToBackgroundTransformer.class;
            case 5:
                return CubeInTransformer.class;
            case 6:
                return CubeOutTransformer.class;
            case 7:
                return DepthPageTransformer.class;
            case 8:
                return FlipHorizontalTransformer.class;
            case 9:
                return FlipVerticalTransformer.class;
            case 10:
                return RotateDownTransformer.class;
            case 11:
                return RotateUpTransformer.class;
            case 12:
                return ScaleInOutTransformer.class;
            case 13:
                return StackTransformer.class;
            case 14:
                return TabletTransformer.class;
            case 15:
                return ZoomInTransformer.class;
            case 16:
                return ZoomOutTranformer.class;
            case 17:
                return ZoomOutSlideTransformer.class;
            default:
                return DefaultTransformer.class;
        }
    }
}
