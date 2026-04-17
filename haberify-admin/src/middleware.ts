import { NextRequest, NextResponse } from "next/server";

const AUTH_COOKIE = "auth_token";
const LOGIN_PATH = "/login";

export function middleware(request: NextRequest) {
    const token = request.cookies.get(AUTH_COOKIE)?.value;
    if (token) return NextResponse.next();

    const { pathname, search } = request.nextUrl;
    const loginUrl = request.nextUrl.clone();
    loginUrl.pathname = LOGIN_PATH;
    loginUrl.search = `?next=${encodeURIComponent(pathname + search)}`;
    return NextResponse.redirect(loginUrl);
}

export const config = {
    matcher: ["/admin", "/admin/:path*", "/profile", "/profile/:path*"],
};
