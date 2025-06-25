import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { catchError, map, of } from 'rxjs';
const _SERVER = 'http://localhost:8000';

@Injectable({
  providedIn: 'root',
})
export class UsuarioService {
  private readonly http = inject(HttpClient);
  constructor() {}

  resetearPassw(id: string) {
    return this.http.patch<any>(`${_SERVER}/api/user/reset/${id}`, {}).pipe(
      map(() => true),
      catchError((error) => {
        return of(error.status);
      })
    );
  }
}
