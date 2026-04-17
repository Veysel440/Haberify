export type ConsentState = "granted" | "denied";

export type ConsentUpdate = {
    ad_storage?: ConsentState;
    ad_user_data?: ConsentState;
    ad_personalization?: ConsentState;
    analytics_storage?: ConsentState;
    functionality_storage?: ConsentState;
    personalization_storage?: ConsentState;
    security_storage?: ConsentState;
};

type DataLayerArgs = Record<string, unknown> | [string, ...unknown[]];

declare global {
    interface Window {
        dataLayer?: DataLayerArgs[];
    }
}

export const GTM_ID = process.env.NEXT_PUBLIC_GTM_ID;
export const CONSENT_STORAGE_KEY = "haberify_consent_v1";

const push = (event: DataLayerArgs): void => {
    if (typeof window === "undefined") return;
    window.dataLayer = window.dataLayer ?? [];
    window.dataLayer.push(event);
};

export const setConsent = (mode: "default" | "update", state: ConsentUpdate): void => {
    push(["consent", mode, state]);
};

export const trackEvent = (name: string, params: Record<string, unknown> = {}): void => {
    push({ event: name, ...params });
};

export const trackArticleView = (article: { id: number | string; slug?: string; title?: string; category?: string | null; language?: string }): void => {
    trackEvent("article_view", {
        article_id: article.id,
        article_slug: article.slug,
        article_title: article.title,
        article_category: article.category,
        article_language: article.language,
    });
};
