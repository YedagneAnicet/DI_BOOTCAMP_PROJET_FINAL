import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UtilisateurService {
  private apiUrl = 'http://localhost:8080/api/utilisateurs';

  private tokenKey = 'auth-token';

  constructor(private http: HttpClient) { }

  inscription(utilisateur: any): Observable<any> {
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
    return this.http.post<any>(`${this.apiUrl}/inscription`, utilisateur, httpOptions);
  }

  connexion(credentials: any): Observable<any> {
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
    return this.http.post<any>(`${this.apiUrl}/connexion`, credentials, httpOptions);
  }


  deconnexion(jeton: string): Observable<any> {
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${jeton}`
      })
    };
    return this.http.post<any>(`${this.apiUrl}/deconnexion`, {}, httpOptions);
  }
}
