type VideoPlayerProps = {
    url: string;
};

export default function VideoPlayer({ url }: VideoPlayerProps) {
    if (!url) return null;
    // YouTube
    if (url.includes("youtube.com") || url.includes("youtu.be")) {
        let videoId = "";
        if (url.includes("youtu.be/")) videoId = url.split("youtu.be/")[1];
        else if (url.includes("v=")) videoId = url.split("v=")[1].split("&")[0];
        if (videoId)
            return (
                <iframe
                    width="100%"
                    height="340"
                    src={`https://www.youtube.com/embed/${videoId}`}
                    title="YouTube video"
                    allowFullScreen
                    className="rounded mb-4"
                />
            );
    }
    // Vimeo
    if (url.includes("vimeo.com")) {
        const videoId = url.split("vimeo.com/")[1];
        return (
            <iframe
                src={`https://player.vimeo.com/video/${videoId}`}
                width="100%"
                height="340"
                className="rounded mb-4"
                allow="autoplay; fullscreen"
                title="Vimeo video"
            />
        );
    }
    // mp4 veya webm
    if (url.match(/\.(mp4|webm)$/))
        return (
            <video src={url} controls className="w-full rounded mb-4" />
        );
    // Diğer (dış link)
    return (
        <a href={url} target="_blank" rel="noopener noreferrer" className="block text-blue-500 underline mb-4">
            Videoyu izle
        </a>
    );
}
