'use client';

import { createContext, useContext, useEffect, useState, ReactNode } from "react";
import * as authApi from "@/services/authApi";

type User = {
    id: number;
    name: string;
    email: string;
    role?: string;
    avatar_url?: string;
};

type AuthContextType = {
    user: User | null;
    setUser: (user: User | null) => void;
    login: (email: string, password: string) => Promise<void>;
    register: (name: string, email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
    loading: boolean;
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {

        authApi.fetchProfile()
            .then(data => setUser(data.user))
            .catch(() => setUser(null))
            .finally(() => setLoading(false));
    }, []);

    const loginHandler = async (email: string, password: string) => {
        setLoading(true);
        const data = await authApi.login(email, password);
        setUser(data.user);
        setLoading(false);
    };

    const registerHandler = async (name: string, email: string, password: string) => {
        setLoading(true);
        const data = await authApi.register(name, email, password);
        setUser(data.user);
        setLoading(false);
    };

    const logoutHandler = async () => {
        await authApi.logout();
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{
            user,
            setUser,
            login: loginHandler,
            register: registerHandler,
            logout: logoutHandler,
            loading,
        }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error("useAuth must be used within AuthProvider");
    return ctx;
}
