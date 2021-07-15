import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AccuracyModalComponent } from './accuracy-modal.component';

describe('AccuracyModalComponent', () => {
  let component: AccuracyModalComponent;
  let fixture: ComponentFixture<AccuracyModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AccuracyModalComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AccuracyModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
