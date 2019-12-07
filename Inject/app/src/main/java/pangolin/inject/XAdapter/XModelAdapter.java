package pangolin.inject.XAdapter;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;

import java.util.List;

import pangolin.inject.R;
import pangolin.inject.XApp;

public class XModelAdapter extends RecyclerView.Adapter<XModelAdapter.ViewHolder> {
    private List<String> mList;

    public XModelAdapter(List<String> mList) {
        this.mList = mList;
    }

    @NonNull
    @Override
    public XModelAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        return new ViewHolder(LayoutInflater.from(parent.getContext()).inflate(R.layout.card_item, null));
    }

    public OnItemClick mOnItemClick;

    public void setOnItemClickListener(OnItemClick onItemClick) {
        this.mOnItemClick = onItemClick;
    }

    public interface OnItemClick {
        void OnClick(int position);
    }

    @Override
    public void onBindViewHolder(@NonNull XModelAdapter.ViewHolder holder, int position) {
        holder.mModel_Title.setText(mList.get(position));
        holder.mModel_Msg.setText(mList.get(position));
        holder.mModel_View.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (mOnItemClick != null)
                    mOnItemClick.OnClick(position);
            }
        });
        Glide.with(XApp.getContext())
                .load(R.drawable.material_design)
                .into(holder.mImageView);
    }

    @Override
    public int getItemCount() {
        return mList.size();
    }

    class ViewHolder extends RecyclerView.ViewHolder {
        private AppCompatImageView mImageView;
        private AppCompatTextView mModel_Msg;
        private AppCompatTextView mModel_Title;
        private RelativeLayout mModel_View;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            mImageView = itemView.findViewById(R.id.model_img);
            mModel_Msg = itemView.findViewById(R.id.model_msg);
            mModel_Title = itemView.findViewById(R.id.model_title);
            mModel_View = itemView.findViewById(R.id.model_view);
        }
    }
}
