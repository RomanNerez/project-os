export const getInitials = (name: string): string => name
    .split(' ')
    .filter(Boolean)
    .map((part: string) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() || '?';
    
/** True for HTML without visible text, e.g. an empty editor's `<p></p>`. */
export const isBlankHtml = (html: string | null | undefined): boolean =>
    !html || html.replace(/<[^>]*>|&nbsp;/g, '').trim() === '';
