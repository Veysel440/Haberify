"use client";
import { useState } from "react";
import { API } from "@/lib/api";

export default function GalleryUpload({ articleId }:{articleId:number}) {
    const [items,setItems]=useState<string[]>([]);
    const upload = async (files: FileList) => {
        const fd = new FormData(); Array.from(files).forEach(f=>fd.append("files[]", f));
        const r = await API.post(`/articles/${articleId}/gallery`, fd, { headers: { "Content-Type":"multipart/form-data" }});
        const urls = (r.data?.data ?? []).map((x:any)=>x.original); setItems(s=>[...s, ...urls]);
    };
    return (
        <div className="space-y-2">
            <label className="block text-sm font-medium">Galeri</label>
            <input type="file" accept="image/*" multiple onChange={e=>e.target.files && upload(e.target.files)} />
            <div className="grid grid-cols-6 gap-2">{items.map((u,i)=><img key={i} src={u} className="h-20 w-full object-cover rounded border" />)}</div>
        </div>
    );
}
