
"use client";
import { useEffect, useState } from "react";
import { API } from "@/lib/api";
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, BarChart, Bar } from "recharts";

export default function AnalyticsPage(){
    const [overview,setOverview]=useState<any>({});
    const [top,setTop]=useState<any[]>([]);
    const [refs,setRefs]=useState<any[]>([]);
    useEffect(()=>{
        (async()=>{
            setOverview((await API.get("/analytics/overview")).data?.data ?? {});
            setTop((await API.get("/analytics/top-articles")).data?.data ?? []);
            setRefs((await API.get("/analytics/referrers")).data?.data ?? []);
        })();
    },[]);
    return (
        <div className="space-y-8">
            <h1 className="text-xl font-semibold">Analitik</h1>

            <section>
                <h2 className="font-medium mb-2">Son Günlük Görünümler</h2>
                <LineChart width={720} height={260} data={overview.views ?? []}>
                    <Line type="monotone" dataKey="count" />
                    <CartesianGrid strokeDasharray="3 3" /><XAxis dataKey="day" /><YAxis /><Tooltip />
                </LineChart>
            </section>

            <section>
                <h2 className="font-medium mb-2">Top Makaleler</h2>
                <BarChart width={720} height={260} data={top}>
                    <Bar dataKey="views" />
                    <CartesianGrid strokeDasharray="3 3" /><XAxis dataKey="title" /><YAxis /><Tooltip />
                </BarChart>
            </section>

            <section>
                <h2 className="font-medium mb-2">Referrers</h2>
                <BarChart width={720} height={260} data={refs}>
                    <Bar dataKey="c" />
                    <CartesianGrid strokeDasharray="3 3" /><XAxis dataKey="ref" /><YAxis /><Tooltip />
                </BarChart>
            </section>
        </div>
    );
}
