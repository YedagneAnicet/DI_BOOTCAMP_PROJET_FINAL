import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListePharmacieComponent } from './liste-pharmacie.component';

describe('ListePharmacieComponent', () => {
  let component: ListePharmacieComponent;
  let fixture: ComponentFixture<ListePharmacieComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListePharmacieComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListePharmacieComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
