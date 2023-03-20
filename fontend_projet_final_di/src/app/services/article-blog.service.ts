import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ArticleBlogService {
  private apiUrl = 'http://localhost:8080/api/articles';

  constructor(private http: HttpClient) {}

    // obtenir la liste de tout les articles
    getAllArticles(): Observable<any> {
      const url = `${this.apiUrl}/all`;
      return this.http.get(url);
    }
}
