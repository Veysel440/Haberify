"use client";
import { useState } from "react";
import { API } from "@/lib/api";

export default function CommentsModerationPage(){
    const [id,setId]=useState<number|''>('');
    const [uid,setUid]=useState<number|''>('');
    const [until,setUntil]=useState<string>("");

    const act = async (path:string, body?:any) => {
        if (!id && path.startsWith("comments/")) return;
        await API.post(`/admin/${path}`, body);
        alert("OK");
    };

    return (
        <div className="space-y-8">
            <section>
                <h1 className="text-xl font-semibold mb-3">Yorum Moderasyonu</h1>
                <div className="flex gap-2 items-center">
                    <input className="border p-2 rounded" placeholder="Yorum ID" value={id} onChange={e=>setId(Number(e.target.value)||'')} />
                    <button className="px-3 py-2 bg-green-600 text-white rounded" onClick={()=>act(`comments/${id}/approve`)}>Onayla</button>
                    <button className="px-3 py-2 bg-yellow-600 text-white rounded" onClick={()=>act(`comments/${id}/reject`)}>Reddet</button>
                    <button className="px-3 py-2 bg-red-600 text-white rounded" onClick={()=>API.delete(`/comments/${id}`).then(()=>alert("OK"))}>Sil</button>
                </div>
            </section>

            <section>
                <h2 className="text-lg font-semibold mb-2">Kullanıcı Yorum Yasağı</h2>
                <div className="flex gap-2 items-center">
                    <input className="border p-2 rounded" placeholder="Kullanıcı ID" value={uid} onChange={e=>setUid(Number(e.target.value)||'')} />
                    <input className="border p-2 rounded" placeholder="Bitiş (ops.) YYYY-MM-DD HH:mm" value={until} onChange={e=>setUntil(e.target.value)} />
                    <button className="px-3 py-2 bg-black text-white rounded" onClick={()=>API.post("/admin/comments/ban",{ user_id: uid, until: until||undefined }).then(()=>alert("OK"))}>Yasakla</button>
                    <button className="px-3 py-2 bg-gray-700 text-white rounded" onClick={()=>API.post(`/admin/comments/unban/${uid}`).then(()=>alert("OK"))}>Yasağı Kaldır</button>
                </div>
            </section>
        </div>
    );
}
