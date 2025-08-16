"use client";
import { useEffect, useState } from "react";
import { API } from "@/lib/api";

export default function NotificationsPage(){
    const [rows,setRows]=useState<any[]>([]);
    const [count,setCount]=useState(0);

    const load=async()=>{
        const r = await API.get("/notifications"); setRows(r.data?.data ?? []);
        const c = await API.get("/notifications/unread-count"); setCount(c.data?.data?.count ?? 0);
    };
    const read=async(id:string)=>{ await API.post(`/notifications/${id}/read`); load(); };

    useEffect(()=>{ load(); },[]);

    return (
        <div className="space-y-3">
            <h1 className="text-xl font-semibold">Bildirimler <span className="text-sm text-gray-500">(okunmamış: {count})</span></h1>
            <ul className="space-y-2">
                {rows.map(n=>(
                    <li key={n.id} className="border rounded p-3 bg-white flex items-center justify-between">
                        <div>
                            <div className="font-medium">{n.type}</div>
                            <div className="text-sm text-gray-600">{n.data?.message ?? "-"}</div>
                        </div>
                        {!n.read_at && <button className="px-3 py-1 border rounded" onClick={()=>read(n.id)}>Okundu</button>}
                    </li>
                ))}
            </ul>
        </div>
    );
}
