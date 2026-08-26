export type MediaID = number;

export interface Media {
    id: MediaID;
    file_name: string;
    size: number;
    mime_type: string;
    origin_url: string;
    preview_url: string;
}

export type UploadedMedia = Media & { isUploaded: boolean }