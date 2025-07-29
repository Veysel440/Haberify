'use client';

import { createContext, useContext, useEffect, useState, ReactNode } from "react";
import { fetchNotifications } from "@/services/notificationApi";

type Notification = {
    id: number;
    type: string;
    title: string;
    message: string | null;
    data: any;
    read: boolean;
    created_at: string;
};

type NotificationContextType = {
    notifications: Notification[];
    reload: () => void;
};

const NotificationContext = createContext<NotificationContextType>({
    notifications: [],
    reload: () => {},
});

export function NotificationProvider({ children }: { children: ReactNode }) {
    const [notifications, setNotifications] = useState<Notification[]>([]);

    const reload = () => {
        fetchNotifications().then(setNotifications);
    };

    useEffect(() => {
        reload();
        const interval = setInterval(reload, 30000);
        return () => clearInterval(interval);
    }, []);

    return (
        <NotificationContext.Provider value={{ notifications, reload }}>
            {children}
        </NotificationContext.Provider>
    );
}

export function useNotifications() {
    return useContext(NotificationContext);
}
