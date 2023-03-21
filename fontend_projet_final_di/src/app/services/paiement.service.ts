import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})

export class PaiementService {
  constructor(private http: HttpClient) {}
  accessPaiement(): Observable<any> {
    return this.http.get('http://localhost:4200/home');
  }
}
