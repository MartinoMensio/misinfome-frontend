import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { Observable } from 'rxjs';
import {map} from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  API_URL = environment.api_url;
  most_popular_entries: Array<string> = [];

  constructor(private httpClient: HttpClient) { }

  getHome() {
    return this.httpClient.get(`${this.API_URL}/frontend/v2/home/`)
  }

  getMostPopularEntries() {
    if (this.most_popular_entries.length) {
      return new Observable((observer) => {
    
        // observable execution
        observer.next(this.most_popular_entries)
        observer.complete()
    })
    } else {
      return this.httpClient.get(`${this.API_URL}/frontend/v2/home/most_popular_entries`).pipe(
        map((result: any) => {
          this.most_popular_entries = result;
          return result;
          }))
        }
  }
}
