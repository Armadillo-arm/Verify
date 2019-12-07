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
using AX_Inject.AuthDialog.api.KfkModel;
using Java.Lang;

namespace AX_Inject.AuthDialog.adapter
{
    public class ProductAdapter : BaseAdapter<KfkPageData.mdata.mproducts>
    {
        private Activity _context;
        private List<KfkPageData.mdata.mproducts> _types;

        public ProductAdapter(Activity _context, List<KfkPageData.mdata.mproducts> _types)
        {
            this._context = _context;
            this._types = _types;
        }

        public override KfkPageData.mdata.mproducts this[int position] => _types[position];

        public override int Count => _types.Count;

        public override long GetItemId(int position) => position;

        public override View GetView(int position, View convertView, ViewGroup parent)
        {
            TextView textView = new TextView(_context);
            textView.Text = _types[position].name;
            textView.TextSize = 18;
            LinearLayout linearLayout = new LinearLayout(_context);
            linearLayout.SetPadding(15, 15, 15, 15);
            linearLayout.AddView(textView);
            return linearLayout;
        }
    }
}