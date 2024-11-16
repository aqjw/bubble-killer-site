const DEFAULT_EXPIRATION_MINUTES = 60 * 24 * 365; // a year

const useSession = {
  prefix: 'mf-',

  set(key, value, expirationMinutes = DEFAULT_EXPIRATION_MINUTES) {
    const now = new Date();
    const expirationTime = now.getTime() + expirationMinutes * 60 * 1000;
    const dataToStore = { value, expirationTime };

    localStorage.setItem(useSession.prefix + key, JSON.stringify(dataToStore));
  },

  get(key, defaultValue = null) {
    const item = localStorage.getItem(useSession.prefix + key);
    if (item) {
      const data = JSON.parse(item);
      const now = new Date().getTime();

      if (data.expirationTime && data.expirationTime < now) {
        useSession.remove(useSession.prefix + key);
        return defaultValue;
      }

      return data.value;
    }
    return defaultValue;
  },

  getOrSet(key, defaultValue, expirationMinutes = DEFAULT_EXPIRATION_MINUTES) {
    const storedValue = useSession.get(useSession.prefix + key);
    if (storedValue !== null) {
      return storedValue;
    }

    useSession.set(useSession.prefix + key, defaultValue, expirationMinutes);
    return defaultValue;
  },

  remove(key) {
    localStorage.removeItem(useSession.prefix + key);
  },
};

export default useSession;
