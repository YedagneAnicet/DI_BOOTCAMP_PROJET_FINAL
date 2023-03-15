import { TestBed } from '@angular/core/testing';

import { GestionPannierService } from './gestion-pannier.service';

describe('GestionPannierService', () => {
  let service: GestionPannierService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GestionPannierService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
