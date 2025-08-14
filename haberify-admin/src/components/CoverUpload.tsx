"use client";
import { useState } from "react";
import api from "@/lib/api";

export default function CoverUpload({ articleId, onDone }: { articleId: number; onDone?: (u:string)=>void; }) {
    const [loading,setLoading] = useState(false);
    const [url,setUrl] = useState<string>();

    const upload = async (f: File) => {
        const fd = new FormData();
        fd.append("file", f);
        setLoading(true);
        try {
            const r = await api.post(`/articles/${articleId}/cover`, fd, { headers:{ "Content-Type":"multipart/form-data" }});
            const u = r?.data?.data?.cover_url ?? r?.data?.cover_url;
            setUrl(u);
            onDone?.(u);
        } finally { setLoading(false); }
    };

    return (
        <div className="space-y-2">
            <label className="block text-sm font-medium">Kapak Yükle</label>
            <input type="file" accept="image/*" onChange={e=>e.target.files?.[0] && upload(e.target.files[0])}/>
            {loading && <p className="text-sm">Yükleniyor…</p>}
            {url && <img src={url} alt="cover" className="h-32 rounded border" />}
        </div>
    );
}
