"use client";
import { useEffect, useState } from "react";
import { API, pickData } from "@/lib/api";
import ArticleForm from "@/components/ArticleForm";
import CoverUpload from "@/components/CoverUpload";
import GalleryUpload from "@/components/GalleryUpload";
import ScheduleDialog from "@/components/ScheduleDialog";

export default function EditArticlePage({ params }: { params: { slug: string } }) {
    const [a,setA]=useState<any|null>(null);
    const load = async()=>{ const r = await API.get(`/articles/${params.slug}`); setA(pickData(r)); };
    useEffect(()=>{ load(); },[params.slug]);
    if (!a) return <p>Yükleniyor…</p>;

    const publish = async()=>{ await API.post(`/articles/${a.id}/publish`); await load(); };
    const feature = async(on:boolean)=>{ await API.post(`/admin/articles/${a.id}/${on?'feature':'unfeature'}`); await load(); };

    return (
        <div className="space-y-6">
            <h1 className="text-xl font-semibold">Düzenle: {a.title}</h1>
            <div className="flex gap-2">
                <button className="px-3 py-1 border rounded" onClick={publish}>Yayınla</button>
                <ScheduleDialog articleId={a.id} onDone={load} />
                <button className="px-3 py-1 border rounded" onClick={()=>feature(!a.is_featured)}>{a.is_featured?'ÖÇ Kaldır':'Öne Çıkar'}</button>
            </div>
            <CoverUpload articleId={a.id} onDone={load} />
            <GalleryUpload articleId={a.id} />
            <ArticleForm mode="edit" initial={a} onSaved={setA} />
        </div>
    );
}
