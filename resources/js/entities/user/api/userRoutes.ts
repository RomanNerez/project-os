export const userRoutes = {
    index: () => '/users',
    store: () => '/users',
    invite: () => '/users/invitations',
    resendInvite: (id: number) => `/users/invitations/${id}/resend`,
    profile: () => '/profile',
    updateProfile: () => '/profile',
    updatePassword: () => '/profile/password',
};
