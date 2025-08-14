"use client";
import { useState } from "react";
import { API } from "@/lib/api";

export default function ScheduleDialog({ articleId, onDone }:{articleId:number; onDone?:()=>void}) {
    const [open,setOpen]=useState(false);
    const [dt,setDt]=useState("");
    const submit = async()=>{ await API.post(`/admin/articles/${articleId}/schedule`, { scheduled_at: dt }); setOpen(false); onDone?.(); };
    if (!open) return <button className="px-3 py-1 border rounded" onClick={()=>setOpen(true)}>Planla</button>;
    return (
        <div className="border rounded p-3 bg-white space-y-2">
            <input className="border p-2 rounded w-full" placeholder="YYYY-MM-DD HH:mm:ss" value={dt} onChange={e=>setDt(e.target.value)} />
            <div className="flex gap-2">
                <button className="px-3 py-1 bg-black text-white rounded" onClick={submit}>Kaydet</button>
                <button className="px-3 py-1 border rounded" onClick={()=>setOpen(false)}>İptal</button>
            </div>
        </div>
    );
}
