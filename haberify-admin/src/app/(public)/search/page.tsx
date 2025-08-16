"use client";
import { useRouter, useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import ArticleCard from "@/components/ArticleCard";
import { api } from "@/lib/public-api";

export default function SearchPage() {
    const qsp = useSearchParams(); const router = useRouter();
    const [q,setQ]=useState(qsp.get("q") || ""); const [rows,setRows]=useState<any[]>([]);
    const run = async(qs: string)=>{ const r = await api.search(qs, 1, { revalidate: false }); setRows(r?.data ?? r ?? []); };
    useEffect(()=>{ const qs = qsp.get("q"); if (qs) { setQ(qs); run(qs); } },[qsp]);

    return (
        <div className="space-y-4">
            <h1 className="text-xl font-semibold">Search</h1>
            <div className="flex gap-2">
                <input className="border p-2 rounded w-full" placeholder="Keyword" value={q} onChange={e=>setQ(e.target.value)} />
                <button className="px-3 py-2 bg-black text-white rounded" onClick={()=>router.push(`/search?q=${encodeURIComponent(q)}`)}>Search</button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {rows.map((a:any)=><ArticleCard key={a.id} a={a} />)}
            </div>
        </div>
    );
}
