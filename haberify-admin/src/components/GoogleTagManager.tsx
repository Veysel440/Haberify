import Script from "next/script";
import { GTM_ID } from "@/lib/gtm";

const consentDefaultScript = `
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{
  ad_storage:'denied',
  ad_user_data:'denied',
  ad_personalization:'denied',
  analytics_storage:'denied',
  functionality_storage:'granted',
  security_storage:'granted',
  wait_for_update: 500
});
`;

const gtmBootstrap = (id: string) => `
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','${id}');
`;

export function GoogleTagManagerHead() {
    if (!GTM_ID) return null;
    return (
        <>
            <Script id="gtm-consent-default" strategy="beforeInteractive">
                {consentDefaultScript}
            </Script>
            <Script id="gtm-init" strategy="afterInteractive">
                {gtmBootstrap(GTM_ID)}
            </Script>
        </>
    );
}

export function GoogleTagManagerNoscript() {
    if (!GTM_ID) return null;
    const src = `https://www.googletagmanager.com/ns.html?id=${encodeURIComponent(GTM_ID)}`;
    return (
        <noscript>
            <iframe src={src} height="0" width="0" style={{ display: "none", visibility: "hidden" }} />
        </noscript>
    );
}
