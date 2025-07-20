// stores/userStore.ts
import { defineStore } from 'pinia'

export interface UserState {
  user: string | null
  loading: boolean
  error: string | null
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    user: null,
    loading: false,
    error: null,
  }),

  actions: {
      async fetchUser() {
        let email = localStorage.getItem('user');

        if (!email || this.user != null) {
          return;
        }

        const res = await fetch('https://localhost:8000/my-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email })
        })

        if (!res.ok) throw new Error('Échec de la connexion')

        const data = await res.json();
        console.log('got user : ', data);
        this.user = data;
      },

      async login(email: string, password: string): Promise<void> {
        this.loading = true
        try {
            const res = await fetch('https://localhost:8000/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            })

            if (!res.ok) throw new Error('Échec de la connexion')

            const data = await res.json();
            console.log('connected as : ', data);
            this.user = data;
            localStorage.setItem('user', data.email)
            this.error = null
        } catch (err: any) {
            this.error = err.message ?? 'Erreur inconnue'
        } finally {
            this.loading = false
        }
    },

    async logout(): Promise<void> {
      await fetch('https://localhost:8000/logout');
      this.user = null
      localStorage.removeItem('user');
      console.log('logged out');

    },
  },
})
