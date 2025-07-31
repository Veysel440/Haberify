'use client';

import { createContext, useContext, useEffect, useState, ReactNode } from "react";

const ThemeContext = createContext<{theme: string, toggleTheme: () => void}>({
    theme: "light", toggleTheme: () => {}
});

export function ThemeProvider({ children }: { children: ReactNode }) {
    const [theme, setTheme] = useState("light");

    useEffect(() => {
        document.documentElement.classList.toggle('dark', theme === "dark");
        localStorage.setItem("theme", theme);
    }, [theme]);

    useEffect(() => {
        setTheme(localStorage.getItem("theme") || "light");
    }, []);

    const toggleTheme = () => setTheme(t => t === "dark" ? "light" : "dark");

    return (
        <ThemeContext.Provider value={{ theme, toggleTheme }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    return useContext(ThemeContext);
}
