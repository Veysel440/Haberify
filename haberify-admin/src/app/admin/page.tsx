"use client";
import { useEffect } from "react";
import { api, setToken } from "@/lib/api";
import { useQuery } from "@tanstack/react-query";

export default function AdminHome() {
    useEffect(()=>{
        const t = localStorage.getItem("token"); if (t) setToken(t); else window.location.href="/login";
    },[]);
    const { data } = useQuery({
        queryKey:["admin-articles"],
        queryFn: async()=> (await api.get("/admin/articles")).data,
    });
    return (
        <main style={{padding:24}}>
            <h2>Admin / Makaleler</h2>
            <table><thead><tr><th>ID</th><th>Başlık</th><th>Durum</th></tr></thead>
                <tbody>
                {data?.data?.data?.map((a:any)=>(
                    <tr key={a.id}><td>{a.id}</td><td>{a.title}</td><td>{a.status}</td></tr>
                ))}
                </tbody></table>
        </main>
    );
}
