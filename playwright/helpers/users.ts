export type DemoRole =
  | 'community_reporter'
  | 'dog_owner'
  | 'veterinarian'
  | 'lgu_official'
  | 'rescue_org'
  | 'admin';

export interface DemoUser {
  key: DemoRole;
  email: string;
  password: string;
  roleLabel: string;
  landingPath: string;
}

export const DEMO_PASSWORD = 'password';

export const USERS: Record<DemoRole, DemoUser> = {
  community_reporter: {
    key: 'community_reporter',
    email: 'maria.santos@email.com',
    password: DEMO_PASSWORD,
    roleLabel: 'Community Reporter',
    landingPath: 'feed.php',
  },
  dog_owner: {
    key: 'dog_owner',
    email: 'rosa.castillo@email.com',
    password: DEMO_PASSWORD,
    roleLabel: 'Dog Owner',
    landingPath: 'feed.php',
  },
  veterinarian: {
    key: 'veterinarian',
    email: 'ana.reyes@email.com',
    password: DEMO_PASSWORD,
    roleLabel: 'Veterinarian',
    landingPath: 'registry.php',
  },
  lgu_official: {
    key: 'lgu_official',
    email: 'luis.cruz@email.com',
    password: DEMO_PASSWORD,
    roleLabel: 'LGU Official',
    landingPath: 'cases.php',
  },
  rescue_org: {
    key: 'rescue_org',
    email: 'rescue@pawdar.org',
    password: DEMO_PASSWORD,
    roleLabel: 'Rescue Organization',
    landingPath: 'rescue.php',
  },
  admin: {
    key: 'admin',
    email: 'admin@pawdar.org',
    password: DEMO_PASSWORD,
    roleLabel: 'Admin',
    landingPath: 'admin.php',
  },
};
