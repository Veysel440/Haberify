import DOMPurify from "isomorphic-dompurify";

const ALLOWED_TAGS = [
    "p", "br", "hr", "blockquote", "pre", "code",
    "ul", "ol", "li",
    "h2", "h3", "h4", "h5", "h6",
    "strong", "b", "em", "i", "u", "span", "small", "sub", "sup", "mark",
    "a", "img", "figure", "figcaption",
    "table", "thead", "tbody", "tr", "th", "td",
];

const ALLOWED_ATTR = [
    "href", "title", "rel", "target",
    "src", "alt", "width", "height",
];

type Props = {
    html?: string | null;
    className?: string;
};

export default function SafeHtml({ html, className }: Props) {
    const clean = DOMPurify.sanitize(html ?? "", {
        ALLOWED_TAGS,
        ALLOWED_ATTR,
        FORBID_TAGS: ["script", "style", "iframe", "object", "embed", "form"],
        FORBID_ATTR: ["onerror", "onload", "onclick"],
    });
    return <div className={className} dangerouslySetInnerHTML={{ __html: clean }} />;
}
