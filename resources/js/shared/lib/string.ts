export const getInitials = (name: string): string => name
    .split(' ')
    .filter(Boolean)
    .map((part: string) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() || '?';
    