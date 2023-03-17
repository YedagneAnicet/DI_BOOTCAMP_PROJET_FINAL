import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class PharmacieService {
  private apiUrl = 'http://localhost:8080/api/pharmacies';

  constructor(private http: HttpClient) {}

  // ajouter une nouvelle pharmacie
  addPharmacy(pharmacy: any): Observable<any> {
    const url = `${this.apiUrl}/add`;
    return this.http.post(url, pharmacy);
  }

  // obtenir la liste de toutes les pharmacies
  getAllPharmacies(): Observable<any> {
    const url = `${this.apiUrl}/all`;
    return this.http.get(url);
  }

  // obtenir une pharmacie spécifique a travers son id
  getPharmacyById(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get(url);
  }

  // obtenir la liste de tout les pharmacie de garde
  getGardePharmacies(): Observable<any> {
    const url = `${this.apiUrl}/all/garde`;
    return this.http.get(url);
  }

  // obtenir la liste de toutes les communes
  getAllCommune(): Observable<any> {
    const url = `${this.apiUrl}/commune`;
    return this.http.get(url);
  }

  // obtenir la liste de toutes les pharmacies par commune
  getPharmacyByCommune(commune: string): Observable<any> {
    const url = `${this.apiUrl}/all/${commune}`;
    return this.http.get(url);
  }

  // obtenir la liste de toutes les pharmacies de garde par commune
  getPharmaciesByVilleAndGarde(ville: string): Observable<any> {
    const url = `${this.apiUrl}/${ville}/garde`;
    return this.http.get(url);
  }

  // modifier une pharmacie enregistrer dans la base de donnée
  updatePharmacy(id: number, pharmacy: any): Observable<any> {
    const url = `${this.apiUrl}/update/${id}`;
    return this.http.put(url, pharmacy).pipe(
      catchError((error) => {
        throw new Error(error.json().error || 'Server error');
      })
    );
  }

  // supprimer une pharmacie de la base de donnee
  deletePharmacy(id: number): Observable<any> {
    const url = `${this.apiUrl}/delete/${id}`;
    return this.http.delete(url).pipe(
      catchError((error) => {
        throw new Error(error.json().error || 'Server error');
      })
    );
  }
}
