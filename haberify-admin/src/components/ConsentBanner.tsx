"use client";
import { useEffect, useState } from "react";
import { CONSENT_STORAGE_KEY, ConsentState, setConsent } from "@/lib/gtm";

type StoredConsent = { analytics: ConsentState; ads: ConsentState; ts: number };

const readStored = (): StoredConsent | null => {
    if (typeof window === "undefined") return null;
    try {
        const raw = localStorage.getItem(CONSENT_STORAGE_KEY);
        return raw ? (JSON.parse(raw) as StoredConsent) : null;
    } catch {
        return null;
    }
};

const apply = (stored: StoredConsent) => {
    setConsent("update", {
        analytics_storage: stored.analytics,
        ad_storage: stored.ads,
        ad_user_data: stored.ads,
        ad_personalization: stored.ads,
    });
};

export default function ConsentBanner() {
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const stored = readStored();
        if (stored) {
            apply(stored);
            return;
        }
        setVisible(true);
    }, []);

    const save = (analytics: ConsentState, ads: ConsentState) => {
        const record: StoredConsent = { analytics, ads, ts: Date.now() };
        localStorage.setItem(CONSENT_STORAGE_KEY, JSON.stringify(record));
        apply(record);
        setVisible(false);
    };

    if (!visible) return null;

    return (
        <div className="fixed inset-x-0 bottom-0 z-50 bg-gray-900 text-white px-4 py-3 shadow-lg">
            <div className="max-w-5xl mx-auto flex flex-col md:flex-row items-start md:items-center gap-3">
                <p className="text-sm flex-1">
                    Sitemizde performans ve reklam çerezleri kullanılabilir. Onayınız olmadan analytics ve reklam çerezleri çalıştırılmaz.
                </p>
                <div className="flex gap-2">
                    <button
                        onClick={() => save("denied", "denied")}
                        className="px-3 py-1.5 rounded bg-gray-700 hover:bg-gray-600 text-sm"
                    >
                        Reddet
                    </button>
                    <button
                        onClick={() => save("granted", "denied")}
                        className="px-3 py-1.5 rounded bg-gray-200 text-gray-900 hover:bg-white text-sm"
                    >
                        Sadece analytics
                    </button>
                    <button
                        onClick={() => save("granted", "granted")}
                        className="px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-500 text-sm"
                    >
                        Tümünü kabul et
                    </button>
                </div>
            </div>
        </div>
    );
}
