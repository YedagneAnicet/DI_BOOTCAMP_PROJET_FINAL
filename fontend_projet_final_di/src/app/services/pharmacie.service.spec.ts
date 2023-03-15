import { TestBed } from '@angular/core/testing';

import { PharmacieService } from './pharmacie.service';

describe('PharmacieService', () => {
  let service: PharmacieService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PharmacieService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
