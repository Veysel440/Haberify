import { NextRequest, NextResponse } from "next/server";
import { revalidateTag } from "next/cache";

export async function POST(req: NextRequest) {
    const { secret, tags } = await req.json().catch(()=>({}));
    if (secret !== process.env.REVALIDATE_SECRET) return NextResponse.json({ ok:false }, { status:401 });
    if (Array.isArray(tags)) tags.forEach(t => t && revalidateTag(String(t)));
    return NextResponse.json({ ok: true, tags: tags ?? [] });
}
